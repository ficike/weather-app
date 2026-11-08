import { useEffect, useState } from "react";
import { Pagination } from "../Pagination.tsx";
import { UnitSystemToggle } from "../UnitSystemToggle.tsx";
import { WeatherTableBody } from "./WeatherTableBody.tsx";
import { WeatherTableFooter } from "./WeatherTableFooter.tsx";
import { WeatherTableHeader } from "./WeatherTableHeader.tsx";
import { useUnitSystem } from "../../contexts/UnitSystemContext.tsx";
import type { WeatherUnitConfig } from "../../utils/weatherUnits.ts";
import type { WeatherRow, WeatherStatistics } from "../../types/weather.ts";

type WeatherTableProps = {
  header: Record<string, string>;
  rows: WeatherRow[];
  statistics: WeatherStatistics;
  unitConfig: WeatherUnitConfig;
};

export function WeatherTable({ header, rows, statistics, unitConfig }: WeatherTableProps) {
  const { unitSystem } = useUnitSystem();
  const rowsPerPage = 10;
  const [page, setPage] = useState(1);
  const pageCount = Math.max(1, Math.ceil(rows.length / rowsPerPage));
  const visibleRows = rows.slice((page - 1) * rowsPerPage, page * rowsPerPage);

  useEffect(() => {
    setPage(1);
  }, [rows]);

  return (
    <div className="mt-6">
      <UnitSystemToggle unitConfig={unitConfig} />
      <div className="overflow-x-auto rounded-lg border-2 border-solid border-amber-500 p-4">
        <table className="w-full border-collapse text-left">
          <WeatherTableHeader header={header} />
          <WeatherTableBody header={header} rows={visibleRows} unitSystem={unitSystem} unitConfig={unitConfig} />
          <WeatherTableFooter header={header} statistics={statistics} unitSystem={unitSystem} unitConfig={unitConfig} />
        </table>
      </div>
      <Pagination page={page} pageCount={pageCount} onPageChange={setPage} />
    </div>
  );
}

