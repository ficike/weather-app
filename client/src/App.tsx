import { useEffect, useRef, useState } from "react";
import { Navigation } from "./components/Navigation.tsx";
import { CityWeather } from "./pages/CityWeather.tsx";
import { RegionWeather } from "./pages/RegionWeather.tsx";
import type { UnitSystem, WeatherUnitConfig } from "./utils/weatherUnits.ts";

function getCurrentPath() {
  return window.location.pathname === "/region-weather" ? "/region-weather" : "/city-weather";
}

export function App() {
  const [currentPath, setCurrentPath] = useState(getCurrentPath);
  const [cities, setCities] = useState<Record<string, string>>({});
  const [regions, setRegions] = useState<Record<string, string>>({});
  const [citiesLoading, setCitiesLoading] = useState(true);
  const [regionsLoading, setRegionsLoading] = useState(true);
  const [citiesError, setCitiesError] = useState<string | null>(null);
  const [regionsError, setRegionsError] = useState<string | null>(null);
  const [unitSystem, setUnitSystem] = useState<UnitSystem>("si");
  const [unitConfig, setUnitConfig] = useState<WeatherUnitConfig | null>(null);
  const [unitConfigError, setUnitConfigError] = useState<string | null>(null);
  const citiesRequested = useRef(false);
  const regionsRequested = useRef(false);
  const unitConfigRequested = useRef(false);

  useEffect(() => {
    if (unitConfigRequested.current) return;
    unitConfigRequested.current = true;

    const baseUrl = import.meta.env.VITE_API_URL;
    void fetch(`${baseUrl}/weather-config`)
      .then(async (response) => {
        if (!response.ok) throw new Error(await response.text());
        setUnitConfig((await response.json()) as WeatherUnitConfig);
      })
      .catch((e: unknown) => {
        setUnitConfigError(`Mértékegységek betöltése sikertelen: ${e instanceof Error ? e.message : String(e)}`);
      });
  }, []);

  useEffect(() => {
    const baseUrl = import.meta.env.VITE_API_URL;
    if (currentPath === "/city-weather" && !citiesRequested.current) {
      citiesRequested.current = true;
      void fetch(`${baseUrl}/cities`)
        .then(async (response) => {
          if (!response.ok) throw new Error(await response.text());
          setCities((await response.json()) as Record<string, string>);
        })
        .catch((e: unknown) => {
          setCitiesError(`Városok betöltése sikertelen: ${e instanceof Error ? e.message : String(e)}`);
        })
        .finally(() => setCitiesLoading(false));
    }

    if (currentPath === "/region-weather" && !regionsRequested.current) {
      regionsRequested.current = true;
      void fetch(`${baseUrl}/regions`)
        .then(async (response) => {
          if (!response.ok) throw new Error(await response.text());
          setRegions((await response.json()) as Record<string, string>);
        })
        .catch((e: unknown) => {
          setRegionsError(`Régiók betöltése sikertelen: ${e instanceof Error ? e.message : String(e)}`);
        })
        .finally(() => setRegionsLoading(false));
    }
  }, [currentPath]);

  useEffect(() => {
    const handlePopState = () => setCurrentPath(getCurrentPath());
    window.addEventListener("popstate", handlePopState);
    return () => window.removeEventListener("popstate", handlePopState);
  }, []);

  const navigate = (path: string) => {
    if (path === window.location.pathname) return;
    window.history.pushState({}, "", path);
    setCurrentPath(path);
  };

  return (
    <>
      <Navigation currentPath={currentPath} onNavigate={navigate} />
      {unitConfigError && <p role="alert" className="mx-auto max-w-4xl px-4 text-sm text-red-500">{unitConfigError}</p>}
      {currentPath === "/region-weather" ? (
        <RegionWeather
          regions={regions}
          optionsLoading={regionsLoading}
          optionsError={regionsError}
          unitSystem={unitSystem}
          onUnitSystemChange={setUnitSystem}
          unitConfig={unitConfig}
        />
      ) : (
        <CityWeather
          cities={cities}
          optionsLoading={citiesLoading}
          optionsError={citiesError}
          unitSystem={unitSystem}
          onUnitSystemChange={setUnitSystem}
          unitConfig={unitConfig}
        />
      )}
    </>
  );
}
