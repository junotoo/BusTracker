import React, { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";

interface DriverBusSelectionProps {
  onBusSelect: (busId: string) => void;
}

const DriverBusSelection: React.FC<DriverBusSelectionProps> = ({ onBusSelect }) => {
  const [buses, setBuses] = useState<string[]>([]);

  // Fetch bus names from the backend
  useEffect(() => {
    const fetchBuses = async () => {
      try {
        const response = await fetch("http://localhost/inc/autocarros.php");
        if (!response.ok) {
          throw new Error("Failed to fetch buses");
        }
        const data = await response.text();
        const busList = data.split(",").filter((bus) => bus.trim() !== ""); // Split and filter empty values
        setBuses(busList);
      } catch (error) {
        console.error("Error fetching buses:", error);
      }
    };

    fetchBuses();
  }, []);

  const handleBusSelect = (busId: string) => {
    console.log(`Driver selected bus: ${busId}`);
    localStorage.setItem("selectedBus", busId); // Store selected bus in local storage
    onBusSelect(busId);
  };

  return (
    <div className="h-screen flex flex-col items-center justify-center">
      <h1 className="text-2xl font-bold mb-4">Select the Bus You Are Driving</h1>
      <div className="space-y-4">
        {buses.length > 0 ? (
          buses.map((bus, index) => (
            <Button key={index} onClick={() => handleBusSelect(bus)}>
              {bus}
            </Button>
          ))
        ) : (
          <p>Loading buses...</p>
        )}
      </div>
    </div>
  );
};

export default DriverBusSelection;