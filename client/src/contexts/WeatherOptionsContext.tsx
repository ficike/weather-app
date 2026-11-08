import { createContext, useCallback, useContext, useRef, useState, type ReactNode } from "react";
import { fetchWeatherOptions, type WeatherOptionsEndpoint } from "../services/weatherOptionsApi.ts";
import { logApiError } from "../utils/apiErrors.ts";

type WeatherOptions = {
  items: Record<string, string>;
  loading: boolean;
  error: string | null;
};

type WeatherOptionsContextValue = {
  cities: WeatherOptions;
  regions: WeatherOptions;
  loadOptions: (endpoint: WeatherOptionsEndpoint) => void;
};

const initialOptions: WeatherOptions = { items: {}, loading: true, error: null };
const WeatherOptionsContext = createContext<WeatherOptionsContextValue | null>(null);

type WeatherOptionsProviderProps = {
  children: ReactNode;
};

export function WeatherOptionsProvider({ children }: WeatherOptionsProviderProps) {
  const [cities, setCities] = useState<WeatherOptions>(initialOptions);
  const [regions, setRegions] = useState<WeatherOptions>(initialOptions);
  const requestedEndpoints = useRef(new Set<WeatherOptionsEndpoint>());

  const loadOptions = useCallback((endpoint: WeatherOptionsEndpoint) => {
    if (requestedEndpoints.current.has(endpoint)) return;
    requestedEndpoints.current.add(endpoint);

    const setOptions = endpoint === "cities" ? setCities : setRegions;

    void fetchWeatherOptions(endpoint)
      .then((items) => {
        setOptions({ items, loading: false, error: null });
      })
      .catch((error: unknown) => {
        logApiError(error);
        setOptions({ items: {}, loading: false, error: getOptionsErrorMessage(endpoint) });
      });
  }, []);

  return (
    <WeatherOptionsContext.Provider value={{ cities, regions, loadOptions }}>
      {children}
    </WeatherOptionsContext.Provider>
  );
}

function getOptionsErrorMessage(endpoint: WeatherOptionsEndpoint): string {
  if (endpoint === "cities") return "A városok betöltése sikertelen. Próbáld újra később.";
  return "A régiók betöltése sikertelen. Próbáld újra később.";
}

export function useWeatherOptionsContext() {
  const context = useContext(WeatherOptionsContext);
  if (!context) {
    throw new Error("useWeatherOptionsContext must be used within WeatherOptionsProvider");
  }
  return context;
}
