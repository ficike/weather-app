import { formatWeatherValue, type UnitSystem, type WeatherUnitConfig } from "../../utils/weatherUnits.ts";
import type { WeatherRow } from "../../types/weather.ts";

type WeatherTableBodyProps = {
  header: Record<string, string>;
  rows: WeatherRow[];
  unitSystem: UnitSystem;
  unitConfig: WeatherUnitConfig;
};

export function WeatherTableBody({ header, rows, unitSystem, unitConfig }: WeatherTableBodyProps) {
  if (rows.length === 0) {
    return (
      <tbody>
        <tr>
          <td colSpan={Object.keys(header).length} className="p-3 text-center text-gray-600">
            Nincs adat a kiválasztott időszakra.
          </td>
        </tr>
      </tbody>
    );
  }

  return (
    <tbody>
      {rows.map((row) => (
        <tr key={row.date} className="border-b border-gray-200 last:border-b-0">
          {Object.keys(header).map((key) => (
            <td key={key} className="p-3">
              {formatWeatherRowValue(key, row, unitSystem, unitConfig)}
            </td>
          ))}
        </tr>
      ))}
    </tbody>
  );
}

function formatWeatherRowValue(
  key: string,
  row: WeatherRow,
  unitSystem: UnitSystem,
  unitConfig: WeatherUnitConfig,
): string {
  if (key === "date") return row.date;

  const value = row[key as keyof WeatherRow];
  if (value === null) return "–";

  return formatWeatherValue(key, value as number, unitSystem, unitConfig);
}
