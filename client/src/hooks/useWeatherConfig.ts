import { useEffect, useRef, useState } from "react";
import { logApiError, throwIfResponseFailed } from "../utils/apiErrors.ts";
import type { WeatherUnitConfig } from "../utils/weatherUnits.ts";

export function useWeatherConfig(enabled: boolean) {
  const [unitConfig, setUnitConfig] = useState<WeatherUnitConfig | null>(null);
  const [unitConfigError, setUnitConfigError] = useState<string | null>(null);
  const requested = useRef(false);

  useEffect(() => {
    if (!enabled) return;
    if (requested.current) return;
    requested.current = true;

    const baseUrl = import.meta.env.VITE_API_URL;
    void fetch(`${baseUrl}/weather-config`)
      .then(async (response) => {
        await throwIfResponseFailed(response);
        setUnitConfig((await response.json()) as WeatherUnitConfig);
      })
      .catch((error: unknown) => {
        logApiError(error);
        setUnitConfigError("A mértékegység-beállítások betöltése sikertelen. Próbáld újra később.");
      });
  }, [enabled]);

  return { unitConfig, unitConfigError };
}
