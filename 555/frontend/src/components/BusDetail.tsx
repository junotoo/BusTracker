
import React from 'react';
import { Bus } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Navigation, Clock, MapPin, X } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

interface BusDetailProps {
  bus: Bus;
  onClose: () => void;
}

const BusDetail: React.FC<BusDetailProps> = ({ bus, onClose }) => {
  return (
    <div className="absolute bottom-4 left-4 right-4 z-10 animate-fade-in">
      <Card>
        <CardHeader className="pb-2">
          <div className="flex items-center justify-between">
            <div className="flex items-center">
              <div className="w-12 h-12 rounded-full bg-accent text-white flex items-center justify-center mr-3">
                <span className="font-bold text-lg">{bus.displayRouteId}</span>
              </div>
              <CardTitle>{bus.displayRouteId}</CardTitle>
            </div>
            <Button 
              variant="ghost" 
              size="icon" 
              onClick={onClose} 
              className="rounded-full h-8 w-8"
            >
              <X className="h-4 w-4" />
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 gap-4">
            <div className="flex items-center space-x-2">
              <MapPin className="h-4 w-4 text-muted-foreground" />
            </div>
          </div>
        
        </CardContent>
        <CardFooter>
          <Button className="w-full">
            Get Directions
          </Button>
        </CardFooter>
      </Card>
    </div>
  );
};

export default BusDetail;
