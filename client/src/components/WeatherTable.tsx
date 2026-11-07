import { useEffect, useState } from "react";
import { Pagination } from "./Pagination.tsx";

export type WeatherRow = {
  date: string;
  min_temp: string | null;
  max_temp: string | null;
  precipitation: string | null;
};

export type WeatherStatistics = Record<"min_temp" | "max_temp" | "precipitation", {
  median: string | null;
  mode: string[];
}>;

type WeatherTableProps = {
  header: Record<string, string>;
  rows: WeatherRow[];
  statistics: WeatherStatistics;
};

export function WeatherTable({ header, rows, statistics }: WeatherTableProps) {
  const rowsPerPage = 10;
  const [page, setPage] = useState(1);
  const pageCount = Math.max(1, Math.ceil(rows.length / rowsPerPage));
  const visibleRows = rows.slice((page - 1) * rowsPerPage, page * rowsPerPage);

  useEffect(() => {
    setPage(1);
  }, [rows]);

  return (
    <div className="mt-6">
      <div className="overflow-x-auto rounded-lg border-2 border-solid border-amber-500 p-4">
        <table className="w-full border-collapse text-left">
        <thead>
          <tr className="border-b-2 border-amber-500">
            {Object.entries(header).map(([key, label]) => (
              <th key={key} scope="col" className="p-3">{label}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.length > 0 ? visibleRows.map((row) => (
            <tr key={row.date} className="border-b border-gray-200 last:border-b-0">
              {Object.keys(header).map((key) => (
                <td key={key} className="p-3">{row[key as keyof WeatherRow] ?? "–"}</td>
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
          <tr>
            <th scope="row" className="p-3">Medián</th>
            <td className="p-3">{statistics.min_temp.median ?? "–"}</td>
            <td className="p-3">{statistics.max_temp.median ?? "–"}</td>
          </tr>
          <tr>
            <th scope="row" className="p-3">Módusz</th>
            <td className="p-3">{statistics.min_temp.mode.join(", ") || "–"}</td>
            <td className="p-3">{statistics.max_temp.mode.join(", ") || "–"}</td>
          </tr>
        </tfoot>
        </table>
      </div>
      <Pagination page={page} pageCount={pageCount} onPageChange={setPage} />
    </div>
  );
}

