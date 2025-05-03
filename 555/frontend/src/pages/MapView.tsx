import React, { useEffect, useState } from 'react';
import MapBox from '@/components/MapBox';
import BusSearch from '@/components/BusSearch';
import BusDetail from '@/components/BusDetail';
import { Bus } from '@/types';
import { LogOut, Settings } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { fetchBusData } from '@/api/busApi';

interface MapViewProps {
  onLogout: () => void;
}

const MapView: React.FC<MapViewProps> = ({ onLogout }) => {
  const [buses, setBuses] = useState<Bus[]>([]);
  const [selectedBus, setSelectedBus] = useState<Bus | null>(null);
  const [userLocation, setUserLocation] = useState<[number, number] | null>(null);

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

  return (
    <div className="h-screen relative">
      <MapBox 
        buses={buses}
        selectedBus={selectedBus}
        onBusSelect={handleBusSelect}
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
