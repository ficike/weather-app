import { WeatherSearchFeedback } from "./WeatherSearchFeedback.tsx";
import { WeatherSearchForm } from "./WeatherSearchForm.tsx";
import { WeatherSearchResults } from "./WeatherSearchResults.tsx";
import { useWeatherSearch } from "../../hooks/useWeatherSearch.ts";

type WeatherSearchProps = {
  entity: "city" | "region";
  entityLabel: string;
  entityId: string;
  options: Record<string, string>;
  optionsLoading: boolean;
  optionsError: string | null;
};

export function WeatherSearch({
  entity,
  entityLabel,
  entityId,
  options,
  optionsLoading,
  optionsError,
}: WeatherSearchProps) {
  const weatherSearch = useWeatherSearch(entity, entityId);

  return (
    <div className="mx-auto mt-2 max-w-4xl px-4">
      <WeatherSearchForm
        entity={entity}
        entityLabel={entityLabel}
        options={options}
        optionsLoading={optionsLoading}
        loading={weatherSearch.loading}
        onSearch={weatherSearch.search}
      >
        <WeatherSearchFeedback
          optionsError={optionsError}
          searchError={weatherSearch.error}
          configError={weatherSearch.unitConfigError}
          hasResult={weatherSearch.result !== null}
          hasUnitConfig={weatherSearch.unitConfig !== null}
        />
      </WeatherSearchForm>
      <WeatherSearchResults result={weatherSearch.result} unitConfig={weatherSearch.unitConfig} />
    </div>
  );
}
