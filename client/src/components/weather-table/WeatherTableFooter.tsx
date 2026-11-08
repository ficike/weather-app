import { formatWeatherValue, type UnitSystem, type WeatherUnitConfig } from "../../utils/weatherUnits.ts";
import type { WeatherStatistics } from "../../types/weather.ts";

type WeatherTableFooterProps = {
  header: Record<string, string>;
  statistics: WeatherStatistics;
  unitSystem: UnitSystem;
  unitConfig: WeatherUnitConfig;
};

type StatisticName = "Medián" | "Módusz";

export function WeatherTableFooter({ header, statistics, unitSystem, unitConfig }: WeatherTableFooterProps) {
  const columns = Object.keys(header).filter((key) => key !== "date");

  return (
    <tfoot className="border-t-2 border-amber-500">
      {(["Medián", "Módusz"] as const).map((statistic) => (
        <tr key={statistic}>
          <th scope="row" className="p-3">{statistic}</th>
          {columns.map((key) => (
            <td key={key} className="p-3">
              {formatStatistic(key, statistic, statistics, unitSystem, unitConfig)}
            </td>
          ))}
        </tr>
      ))}
    </tfoot>
  );
}

function formatStatistic(
  key: string,
  statistic: StatisticName,
  statistics: WeatherStatistics,
  unitSystem: UnitSystem,
  unitConfig: WeatherUnitConfig,
): string {
  const values = statistics[key as keyof WeatherStatistics];
  if (!values) return "–";

  if (statistic === "Medián") {
    return values.median === null
      ? "–"
      : formatWeatherValue(key, values.median, unitSystem, unitConfig);
  }

  if (values.mode.length === 0) return "–";
  return values.mode
    .map((value) => formatWeatherValue(key, value, unitSystem, unitConfig))
    .join(", ");
}
