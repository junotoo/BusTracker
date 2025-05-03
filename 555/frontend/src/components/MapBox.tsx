import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import { Bus } from '@/types';
import { useToast } from '@/components/ui/use-toast';

const MAPBOX_TOKEN = 'pk.eyJ1IjoiM2RzYnJvczY0IiwiYSI6ImNtYTVmZGs0aTBmcGIybHNkOTQxOGNsamkifQ.Vy2Lfw9Nmd-5l3ByYL6ZEA';

interface MapBoxProps {
  buses: Bus[];
  selectedBus: Bus | null;
  onBusSelect: (bus: Bus | null) => void;
}

const MapBox: React.FC<MapBoxProps> = ({ buses, selectedBus, onBusSelect }) => {
  const mapContainer = useRef<HTMLDivElement | null>(null);
  const map = useRef<mapboxgl.Map | null>(null);
  const userMarkerRef = useRef<mapboxgl.Marker | null>(null);
  const busMarkersRef = useRef<Record<string, mapboxgl.Marker>>({});
  const [userLocation, setUserLocation] = useState<[number, number] | null>(null);
  const { toast } = useToast();

  const isDriver = localStorage.getItem('isDriver') === 'true';
  const drivingBus = localStorage.getItem('selectedBus'); // Get the bus name the driver is driving

  // Initialize map
  useEffect(() => {
    if (!mapContainer.current || map.current) return;

    mapboxgl.accessToken = MAPBOX_TOKEN;

    map.current = new mapboxgl.Map({
      container: mapContainer.current,
      style: 'mapbox://styles/mapbox/streets-v11',
      center: [-8.42, 40.21], // Default location
      zoom: 12,
      pitch: 45,
      bearing: -17.6,
      antialias: true,
      attributionControl: false
    });

    map.current.addControl(
      new mapboxgl.NavigationControl({
        visualizePitch: true,
      }),
      'top-right'
    );

    // Add 3D buildings when style loads
    map.current.on('style.load', () => {
      // Insert the layer beneath any symbol layer.
      const layers = map.current?.getStyle().layers;
      if (!layers) return;
      
      const labelLayerId = layers.find(
        (layer) => layer.type === 'symbol' && layer.layout && layer.layout['text-field']
      )?.id;

      if (!labelLayerId) return;
      
      map.current?.addLayer(
        {
          'id': 'add-3d-buildings',
          'source': 'composite',
          'source-layer': 'building',
          'filter': ['==', 'extrude', 'true'],
          'type': 'fill-extrusion',
          'minzoom': 15,
          'paint': {
            'fill-extrusion-color': '#aaa',
            'fill-extrusion-height': [
              'interpolate',
              ['linear'],
              ['zoom'],
              15,
              0,
              15.05,
              ['get', 'height']
            ],
            'fill-extrusion-base': [
              'interpolate',
              ['linear'],
              ['zoom'],
              15,
              0,
              15.05,
              ['get', 'min_height']
            ],
            'fill-extrusion-opacity': 0.6
          }
        },
        labelLayerId
      );
    });

    // Get user location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          setUserLocation([longitude, latitude]);

          if (map.current) {
            map.current.flyTo({
              center: [longitude, latitude],
              zoom: 15,
              speed: 1.2,
              curve: 1.42
            });
          }


          // Insert driver's bus location if the user is a driver
          if (isDriver) {
            insertBusLocation(drivingBus, latitude, longitude);
          }
        },
        (error) => {
          console.error("Error getting location:", error.message);
          toast({
            title: "Location Error",
            description: "Could not access your location. Some features may be limited.",
            variant: "destructive"
          });
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        }
      );
    }

    return () => {
      if (map.current) {
        map.current.remove();
        map.current = null;
      }
    };
  }, [toast, isDriver, drivingBus]);

  // Function to insert bus location into the backend
  const insertBusLocation = async (busName: string, lat: number, lng: number) => {
    try {
      const response = await fetch(`http://localhost/inc/businsert.php?name=${busName}&lat=${lat}&lng=${lng}`);
      if (!response.ok) {
        throw new Error('Failed to insert bus location');
      }
      const data = await response.text();
      console.log('Bus location inserted:', data);
    } catch (error) {
      console.error('Error inserting bus location:', error);
    }
  };

  // Add/update user marker
  useEffect(() => {
    if (!map.current || !userLocation) return;

    // Create user marker if it doesn't exist
    if (!userMarkerRef.current) {
      const markerElement = document.createElement('div');
      markerElement.className = 'marker-pulse';

      userMarkerRef.current = new mapboxgl.Marker(markerElement)
        .setLngLat(userLocation)
        .addTo(map.current);
    } else {
      userMarkerRef.current.setLngLat(userLocation);
    }
  }, [userLocation]);

  // Add/update bus markers
  useEffect(() => {
    if (!map.current) return;

    // Remove old markers
    Object.values(busMarkersRef.current).forEach(marker => marker.remove());
    busMarkersRef.current = {};

    // Add new markers for each bus
    buses.forEach(bus => {
      const lat = bus.lat;
      const lng = bus.lng;

      const element = document.createElement('div');
      element.className = 'bus-marker';
      element.innerHTML = bus.displayRouteId; // Use displayRouteId
      element.style.backgroundColor = selectedBus?.id === bus.id
        ? 'hsl(var(--secondary))'
        : 'hsl(var(--accent))';

      element.addEventListener('click', () => {
        onBusSelect(bus);
        if (map.current) {
          map.current.flyTo({
            center: [lng, lat],
            zoom: 15,
          });
        }
      });

      const marker = new mapboxgl.Marker(element)
        .setLngLat([lng, lat])
        .addTo(map.current!);

      busMarkersRef.current[bus.id] = marker;
    });
  }, [buses, selectedBus, onBusSelect]);

  // Fly to selected bus
  useEffect(() => {
    if (!map.current || !selectedBus) return;
    const lat = selectedBus.lat;
    const lng = selectedBus.lng;
    map.current.flyTo({
      center: [lng, lat],
      zoom: 15,
      speed: 1.2
    });
  }, [selectedBus]);

  return <div ref={mapContainer} className="w-full h-full rounded-lg shadow-lg" />;
};

export default MapBox;
