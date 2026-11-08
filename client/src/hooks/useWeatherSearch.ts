import { useCallback, useState } from "react";
import { useWeatherConfig } from "./useWeatherConfig.ts";
import { fetchWeather } from "../services/weatherApi.ts";
import { logApiError } from "../utils/apiErrors.ts";
import type { WeatherResponse } from "../types/weather.ts";

type WeatherEntity = "city" | "region";

type SearchParams = {
  selectedEntityId: string;
  startDate: string;
  endDate: string;
};

export function useWeatherSearch(entity: WeatherEntity, entityId: string) {
  const [result, setResult] = useState<WeatherResponse | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const { unitConfig, unitConfigError } = useWeatherConfig(result !== null);

  const search = useCallback(async ({ selectedEntityId, startDate, endDate }: SearchParams) => {
    setError(null);
    setResult(null);
    setLoading(true);

    try {
      setResult(await fetchWeather({ entity, entityId, selectedEntityId, startDate, endDate }));
    } catch (requestError) {
      logApiError(requestError);
      setError("Az időjárási adatok betöltése sikertelen. Próbáld újra később.");
    } finally {
      setLoading(false);
    }
  }, [entity, entityId]);

  return { result, error, loading, search, unitConfig, unitConfigError };
}
