
export interface Bus {
  id: number;
  route_id: string;
  lat: number;
  lng: number;
  displayRouteId: string;
}

export interface User {
  email: string;
}

export type AuthFormType = 'login' | 'signup';
