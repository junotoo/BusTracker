
import React from 'react';
import MapView from './MapView';

interface IndexProps {
  onLogout: () => void;
}

const Index = ({ onLogout }: IndexProps) => {
  return <MapView onLogout={onLogout} />;
};

export default Index;

