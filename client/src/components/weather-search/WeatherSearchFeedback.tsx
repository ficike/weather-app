type WeatherSearchFeedbackProps = {
  optionsError: string | null;
  searchError: string | null;
  configError: string | null;
  hasResult: boolean;
  hasUnitConfig: boolean;
};

export function WeatherSearchFeedback({
  optionsError,
  searchError,
  configError,
  hasResult,
  hasUnitConfig,
}: WeatherSearchFeedbackProps) {
  const error = optionsError ?? searchError ?? configError;

  return (
    <>
      {error && <div role="alert" className="mt-2 text-sm text-red-500">{error}</div>}
      {hasResult && !hasUnitConfig && !configError && (
        <p role="status" className="mt-2 text-sm text-gray-600">
          A táblázat beállításainak betöltése…
        </p>
      )}
    </>
  );
}
