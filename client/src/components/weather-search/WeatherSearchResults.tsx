import { WeatherTable } from "../weather-table/WeatherTable.tsx";
import type { WeatherResponse } from "../../types/weather.ts";
import type { WeatherUnitConfig } from "../../utils/weatherUnits.ts";

type WeatherSearchResultsProps = {
  result: WeatherResponse | null;
  unitConfig: WeatherUnitConfig | null;
};

export function WeatherSearchResults({ result, unitConfig }: WeatherSearchResultsProps) {
  if (result === null || unitConfig === null) return null;

  return (
    <WeatherTable
      header={result.header}
      rows={result.data}
      statistics={result.statistics}
      unitConfig={unitConfig}
    />
  );
}
