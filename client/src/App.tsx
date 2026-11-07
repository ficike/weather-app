import { useEffect, useRef, useState } from "react";
import { Navigation } from "./components/Navigation.tsx";
import { CityWeather } from "./pages/CityWeather.tsx";
import { RegionWeather } from "./pages/RegionWeather.tsx";

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
  const citiesRequested = useRef(false);
  const regionsRequested = useRef(false);

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
      {currentPath === "/region-weather" ? (
        <RegionWeather regions={regions} optionsLoading={regionsLoading} optionsError={regionsError} />
      ) : (
        <CityWeather cities={cities} optionsLoading={citiesLoading} optionsError={citiesError} />
      )}
    </>
  );
}
