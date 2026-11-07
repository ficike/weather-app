import { useEffect, useState } from "react";
import { DateField } from "../components/forms/DateField.tsx";
import { SearchButton } from "../components/forms/SearchButton.tsx";
import { Select } from "../components/forms/Select.tsx";
import { WeatherTable, type WeatherRow, type WeatherStatistics } from "../components/WeatherTable.tsx";
import type { UnitSystem, WeatherUnitConfig } from "../utils/weatherUnits.ts";

type RegionWeatherResponse = {
  header: Record<string, string>;
  data: WeatherRow[];
  statistics: WeatherStatistics;
};

type RegionWeatherProps = {
  regions: Record<string, string>;
  optionsLoading: boolean;
  optionsError: string | null;
  unitSystem: UnitSystem;
  onUnitSystemChange: (unitSystem: UnitSystem) => void;
  unitConfig: WeatherUnitConfig | null;
};

export function RegionWeather({ regions, optionsLoading, optionsError, unitSystem, onUnitSystemChange, unitConfig }: RegionWeatherProps) {
  const [region, setRegion] = useState("");
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [result, setResult] = useState<RegionWeatherResponse | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!region) setRegion(Object.keys(regions)[0] ?? "");
  }, [regions, region]);

  const onSearch = async () => {
    setError(null);
    setResult(null);
    setLoading(true);

    try {
      const params = new URLSearchParams({
        region,
        startDate: startDate.replaceAll("-", ""),
        endDate: endDate.replaceAll("-", ""),
      });
      const baseUrl = import.meta.env.VITE_API_URL;
      const response = await fetch(`${baseUrl}/region-weather?${params}`);
      if (!response.ok) throw new Error(await response.text());

      setResult((await response.json()) as RegionWeatherResponse);
    } catch (e) {
      setError(`Hiba: ${e instanceof Error ? e.message : String(e)}`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="mx-auto mt-2 max-w-4xl px-4">
      <div className="mx-auto max-w-md rounded-lg border-2 border-solid border-amber-500 px-8 py-4">
        <div className="flex flex-col gap-2">
          <Select
            id="region"
            label="Régió"
            options={regions}
            value={region}
            loading={optionsLoading}
            loadingMessage="Régiók betöltése…"
            onChange={setRegion}
          />
          <DateField id="start-date" label="Kezdő dátum" value={startDate} onChange={setStartDate} />
          <DateField id="end-date" label="Vég dátum" value={endDate} onChange={setEndDate} />
          <SearchButton
            loading={loading}
            disabled={optionsLoading || !region || !startDate || !endDate}
            onClick={onSearch}
          />
        </div>
        {(optionsError || error) && <div role="alert" className="mt-2 text-sm text-red-500">{optionsError ?? error}</div>}
      </div>
      {result !== null && unitConfig !== null && (
        <WeatherTable
          header={result.header}
          rows={result.data}
          statistics={result.statistics}
          unitSystem={unitSystem}
          onUnitSystemChange={onUnitSystemChange}
          unitConfig={unitConfig}
        />
      )}
    </div>
  );
}
