import { useEffect, useState, type ReactNode } from "react";
import { DateField } from "../forms/DateField.tsx";
import { SearchButton } from "../forms/SearchButton.tsx";
import { Select } from "../forms/Select.tsx";

type SearchParams = {
  selectedEntityId: string;
  startDate: string;
  endDate: string;
};

type WeatherSearchFormProps = {
  entity: "city" | "region";
  entityLabel: string;
  options: Record<string, string>;
  optionsLoading: boolean;
  loading: boolean;
  onSearch: (params: SearchParams) => void | Promise<void>;
  children?: ReactNode;
};

export function WeatherSearchForm({
  entity,
  entityLabel,
  options,
  optionsLoading,
  loading,
  onSearch,
  children,
}: WeatherSearchFormProps) {
  const [selectedEntityId, setSelectedEntityId] = useState("");
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");

  useEffect(() => {
    if (!selectedEntityId) setSelectedEntityId(Object.keys(options)[0] ?? "");
  }, [options, selectedEntityId]);

  const handleSearch = () => onSearch({ selectedEntityId, startDate, endDate });

  return (
    <div className="mx-auto max-w-md rounded-lg border-2 border-solid border-amber-500 px-8 py-4">
      <div className="flex flex-col gap-2">
        <Select
          id={entity}
          label={entityLabel}
          options={options}
          value={selectedEntityId}
          loading={optionsLoading}
          onChange={setSelectedEntityId}
        />
        <DateField id="start-date" label="Kezdő dátum" value={startDate} onChange={setStartDate} />
        <DateField id="end-date" label="Vég dátum" value={endDate} onChange={setEndDate} />
        <SearchButton
          loading={loading}
          disabled={optionsLoading || !selectedEntityId || !startDate || !endDate}
          onClick={() => void handleSearch()}
        />
      </div>
      {children}
    </div>
  );
}
