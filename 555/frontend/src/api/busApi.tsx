const routeIdMapping: Record<number, string> = {
  1: '2T',
  3: '4',
  5: '5',
  7: '38',
};

export const fetchBusData = async () => {
  try {
    console.log("Starting fetchBusData...");
    const buses = [];

    for (let route_id = 1; route_id <= 8; route_id += 2) {
      console.log(`Fetching data for route_id=${route_id}`);
      const response = await fetch(`http://localhost/inc/busget.php?route_id=${route_id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        console.warn(`Failed to fetch data for route_id=${route_id}`);
        continue;
      }

      const data = await response.json();
      console.log(`Data for route_id=${route_id}:`, data);

      if (data && !data.error) {
        buses.push({
          ...data,
          displayRouteId: routeIdMapping[parseInt(data.route_id, 10)], // Map route_id to displayRouteId
        });
      }
    }

    console.log('Fetched buses:', buses);
    return buses;
  } catch (error) {
    console.error('Error fetching bus data:', error);
    return [];
  }
};