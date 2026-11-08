import { throwIfResponseFailed } from "../utils/apiErrors.ts";

export type WeatherOptionsEndpoint = "cities" | "regions";

export async function fetchWeatherOptions(endpoint: WeatherOptionsEndpoint): Promise<Record<string, string>> {
  const baseUrl = import.meta.env.VITE_API_URL;
  const response = await fetch(`${baseUrl}/${endpoint}`);
  await throwIfResponseFailed(response);

  return (await response.json()) as Record<string, string>;
}
