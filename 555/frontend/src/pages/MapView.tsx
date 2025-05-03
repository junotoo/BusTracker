import React, { useEffect, useState } from 'react';
import MapBox from '@/components/MapBox';
import BusSearch from '@/components/BusSearch';
import BusDetail from '@/components/BusDetail';
import { Bus } from '@/types';
import { LogOut, Settings } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { fetchBusData } from '@/api/busApi';

const MAPBOX_TOKEN = 'pk.eyJ1IjoiM2RzYnJvczY0IiwiYSI6ImNtYTVmZGs0aTBmcGIybHNkOTQxOGNsamkifQ.Vy2Lfw9Nmd-5l3ByYL6ZEA';

interface MapViewProps {
  onLogout: () => void;
}

const MapView: React.FC<MapViewProps> = ({ onLogout }) => {
  const [buses, setBuses] = useState<Bus[]>([]);
  const [selectedBus, setSelectedBus] = useState<Bus | null>(null);
  const [userLocation, setUserLocation] = useState<[number, number] | null>(null);
  const [routeCoordinates, setRouteCoordinates] = useState<[number, number][]>([]); // Add this state

  useEffect(() => {
    // Function to fetch bus data
    const fetchData = async () => {
      console.log("Fetching bus data...");
      try {
        const buses = await fetchBusData();
        setBuses(buses);
        console.log(buses);
      } catch (error) {
        console.error("Error fetching bus data:", error);
      }
    };

    // Get user location and fetch initial bus data
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const { latitude, longitude } = position.coords;
          setUserLocation([longitude, latitude]);
          fetchData(); // Fetch initial bus data
        },
        async (error) => {
          console.error("Error getting location:", error.message);

          // Fallback to default location and fetch buses
          const defaultLocation: [number, number] = [-8.42, 40.21];
          setUserLocation(defaultLocation);
          fetchData(); // Fetch initial bus data
        }
      );
    } else {
      console.warn("Geolocation is not supported by this browser.");
      const defaultLocation: [number, number] = [-8.42, 40.21];
      setUserLocation(defaultLocation);
      fetchData(); // Fetch initial bus data
    }

    // Fetch bus data every 15 seconds
    const interval = setInterval(() => {
      fetchData();
    }, 6000);

    return () => clearInterval(interval); // Cleanup interval on component unmount
  }, []);

  const handleBusSelect = (bus: Bus | null) => {
    setSelectedBus(bus);
  };

  const handleGetDirections = async () => {
    if (selectedBus) {
      try {
        console.log(`Fetching route for bus with route_id=${selectedBus.route_id}`);
        
        // Fetch route data from the PHP endpoint
        const response = await fetch(`http://localhost/inc/routeget.php?route_id=${selectedBus.route_id}`);
        if (!response.ok) {
          throw new Error('Failed to fetch route data');
        }
  
        const routeData = await response.json();
        const coordinates = routeData.map((stop: any) => [parseFloat(stop.lon), parseFloat(stop.lat)]);
  
        console.log('Route coordinates:', coordinates);
  
        // Use Mapbox Directions API to calculate the driving route
        const directionsResponse = await fetch(
          `https://api.mapbox.com/directions/v5/mapbox/driving/${coordinates
            .map(coord => coord.join(','))
            .join(';')}?geometries=geojson&overview=full&access_token=${MAPBOX_TOKEN}`
        );
  
        if (!directionsResponse.ok) {
          throw new Error('Failed to fetch directions from Mapbox API');
        }
  
        const directionsData = await directionsResponse.json();
        const route = directionsData.routes[0].geometry.coordinates;
  
        console.log('Driving route:', route);
  
        setRouteCoordinates(route); // Update the routeCoordinates state
      } catch (error) {
        console.error('Error fetching or drawing route:', error);
      }
    } else {
      console.log('No bus selected.');
    }
  };

  return (
    <div className="h-screen relative">
      <MapBox 
        buses={buses}
        selectedBus={selectedBus}
        onBusSelect={handleBusSelect}
        routeCoordinates={routeCoordinates} // Pass routeCoordinates to MapBox
      />
      
      <BusSearch 
        buses={buses}
        selectedBus={selectedBus}
        onBusSelect={handleBusSelect}
      />
      
      {selectedBus && (
        <BusDetail 
          bus={selectedBus} 
          onClose={() => setSelectedBus(null)} 
          onGetDirections={handleGetDirections} // Pass the new prop
        />
      )}
      
      <div className="absolute top-[20%] right-4 z-10 flex flex-col space-y-2">
        <Button 
          variant="outline" 
          size="icon"
          className="bg-white shadow-md"
        >
          <Settings className="h-4 w-4" />
        </Button>
        <Button 
          variant="outline" 
          size="icon"
          className="bg-white shadow-md"
          onClick={onLogout}
        >
          <LogOut className="h-4 w-4" />
        </Button>
      </div>
    </div>
  );
};

export default MapView;
