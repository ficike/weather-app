import { throwIfResponseFailed } from "../utils/apiErrors.ts";
import type { WeatherResponse } from "../types/weather.ts";

type WeatherEntity = "city" | "region";

type WeatherSearchParams = {
  entity: WeatherEntity;
  entityId: string;
  selectedEntityId: string;
  startDate: string;
  endDate: string;
};

export async function fetchWeather({
  entity,
  entityId,
  selectedEntityId,
  startDate,
  endDate,
}: WeatherSearchParams): Promise<WeatherResponse> {
  const params = new URLSearchParams({
    [entityId]: selectedEntityId,
    startDate: startDate.replaceAll("-", ""),
    endDate: endDate.replaceAll("-", ""),
  });
  const baseUrl = import.meta.env.VITE_API_URL;
  const response = await fetch(`${baseUrl}/${entity}-weather?${params}`);
  await throwIfResponseFailed(response);

  return (await response.json()) as WeatherResponse;
}
