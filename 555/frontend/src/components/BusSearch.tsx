import React, { useState, useEffect } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { 
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from '@/components/ui/sheet';
import { Bus } from '@/types';
import { Search, Bus as BusIcon } from 'lucide-react';

interface BusSearchProps {
  buses: Bus[];
  onBusSelect: (bus: Bus | null) => void;
  selectedBus: Bus | null;
}

const BusSearch: React.FC<BusSearchProps> = ({ buses, onBusSelect, selectedBus }) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [filteredBuses, setFilteredBuses] = useState<Bus[]>(buses);
  
  useEffect(() => {
    if (!searchQuery) {
      setFilteredBuses(buses);
      return;
    }
    
    const filtered = buses.filter(bus =>
      bus.displayRouteId.toLowerCase().includes(searchQuery.toLowerCase()) // Use displayRouteId
    );
    
    setFilteredBuses(filtered);
  }, [searchQuery, buses]);
  
  return (
    <Sheet>
      <SheetTrigger asChild>
        <Button 
          variant="outline" 
          className="absolute top-4 left-4 z-10 bg-white shadow-md"
        >
          <Search className="w-4 h-4 mr-2" />
          Search Buses
        </Button>
      </SheetTrigger>
      <SheetContent side="left">
        <SheetHeader>
          <SheetTitle>Find a Bus</SheetTitle>
        </SheetHeader>
        <div className="py-4">
          <Input
            placeholder="Search by route number (e.g., 37, 24T)"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="mb-4"
          />
          <div className="space-y-2">
            {filteredBuses.length > 0 ? (
              filteredBuses.map(bus => (
                <BusItem 
                  key={bus.id}
                  bus={bus}
                  isSelected={selectedBus?.id === bus.id}
                  onSelect={() => onBusSelect(bus)}
                />
              ))
            ) : (
              <div className="text-center py-8 text-muted-foreground">
                No buses found matching "{searchQuery}"
              </div>
            )}
          </div>
        </div>
      </SheetContent>
    </Sheet>
  );
};

interface BusItemProps {
  bus: Bus;
  isSelected: boolean;
  onSelect: () => void;
}

const BusItem: React.FC<BusItemProps> = ({ bus, isSelected, onSelect }) => {
  return (
    <div 
      className={`p-3 rounded-md cursor-pointer flex items-center justify-between transition-all ${
        isSelected 
          ? 'bg-primary/10 border border-primary/30' 
          : 'hover:bg-accent/10 border border-transparent'
      }`}
      onClick={onSelect}
    >
      <div className="flex items-center space-x-3">
        <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
          isSelected ? 'bg-primary text-white' : 'bg-accent text-white'
        }`}>
          <span className="font-bold">{bus.displayRouteId}</span> {/* Use displayRouteId */}
        </div>
        <div>
          <h4 className="font-medium">{bus.displayRouteId}</h4> {/* Use displayRouteId */}
        </div>
      </div>
      <div className="flex flex-col items-end">
        <BusIcon className="h-4 w-4 text-muted-foreground" />
      </div>
    </div>
  );
};

export default BusSearch;
