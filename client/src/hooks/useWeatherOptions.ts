import { useEffect } from "react";
import { useWeatherOptionsContext } from "../contexts/WeatherOptionsContext.tsx";

export function useWeatherOptions(endpoint: "cities" | "regions") {
  const { cities, regions, loadOptions } = useWeatherOptionsContext();

  useEffect(() => {
    loadOptions(endpoint);
  }, [endpoint, loadOptions]);

  return endpoint === "cities" ? cities : regions;
}
