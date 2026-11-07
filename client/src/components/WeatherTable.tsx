import { useEffect, useState } from "react";
import { Pagination } from "./Pagination.tsx";
import { UnitSystemToggle } from "./UnitSystemToggle.tsx";
import { formatWeatherValue, type UnitSystem, type WeatherUnitConfig } from "../utils/weatherUnits.ts";

export type WeatherRow = {
  date: string;
  min_temp: number | null;
  max_temp: number | null;
  precipitation: number | null;
};

export type WeatherStatistics = Partial<Record<"min_temp" | "max_temp" | "precipitation", {
  median: number | null;
  mode: number[];
}>>;

type WeatherTableProps = {
  header: Record<string, string>;
  rows: WeatherRow[];
  statistics: WeatherStatistics;
  unitSystem: UnitSystem;
  onUnitSystemChange: (unitSystem: UnitSystem) => void;
  unitConfig: WeatherUnitConfig;
};

export function WeatherTable({ header, rows, statistics, unitSystem, onUnitSystemChange, unitConfig }: WeatherTableProps) {
  const rowsPerPage = 10;
  const [page, setPage] = useState(1);
  const pageCount = Math.max(1, Math.ceil(rows.length / rowsPerPage));
  const visibleRows = rows.slice((page - 1) * rowsPerPage, page * rowsPerPage);

  useEffect(() => {
    setPage(1);
  }, [rows]);

  return (
    <div className="mt-6">
      <UnitSystemToggle value={unitSystem} unitConfig={unitConfig} onChange={onUnitSystemChange} />
      <div className="overflow-x-auto rounded-lg border-2 border-solid border-amber-500 p-4">
        <table className="w-full border-collapse text-left">
        <thead>
          <tr className="border-b-2 border-amber-500">
            {Object.entries(header).map(([key, label]) => (
              <th key={key} scope="col" className="p-2">{label}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.length > 0 ? visibleRows.map((row) => (
            <tr key={row.date} className="border-b border-gray-200 last:border-b-0">
              {Object.keys(header).map((key) => (
                <td key={key} className="p-3">
                  {key === "date"
                    ? row.date
                    : row[key as keyof WeatherRow] === null
                      ? "–"
                      : formatWeatherValue(key, row[key as "min_temp" | "max_temp" | "precipitation"] as number, unitSystem, unitConfig)}
                </td>
              ))}
            </tr>
          )) : (
            <tr>
              <td colSpan={Object.keys(header).length} className="p-3 text-center text-gray-600">
                Nincs adat a kiválasztott időszakra.
              </td>
            </tr>
          )}
        </tbody>
        <tfoot className="border-t-2 border-amber-500">
          {(["Medián", "Módusz"] as const).map((statistic) => (
            <tr key={statistic}>
              <th scope="row" className="p-3">{statistic}</th>
              {Object.keys(header).filter((key) => key !== "date").map((key) => {
                const values = statistics[key as keyof WeatherStatistics];
                const formatted = !values
                  ? "–"
                  : statistic === "Medián"
                    ? values.median === null ? "–" : formatWeatherValue(key, values.median, unitSystem, unitConfig)
                    : values.mode.length === 0
                    ? "–"
                    : values.mode.map((value) => formatWeatherValue(key, value, unitSystem, unitConfig)).join(", ");
                return <td key={key} className="p-3">{formatted}</td>;
              })}
            </tr>
          ))}
        </tfoot>
        </table>
      </div>
      <Pagination page={page} pageCount={pageCount} onPageChange={setPage} />
    </div>
  );
}

