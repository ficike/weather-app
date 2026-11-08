type SelectProps = {
  id: string;
  label: string;
  options: Record<string, string>;
  value: string;
  loading?: boolean;
  loadingMessage?: string;
  onChange: (value: string) => void;
};

export function Select({
  id,
  label,
  options,
  value,
  loading = false,
  loadingMessage = "Elemek betöltése…",
  onChange,
}: SelectProps) {
  return (
    <div className="flex flex-col gap-2">
      <label htmlFor={id} className="font-bold">{label}</label>
      <select
        id={id}
        value={value}
        onChange={(event) => onChange(event.target.value)}
        disabled={loading || Object.keys(options).length === 0}
        className="rounded-md bg-gray-100 p-2 disabled:opacity-50"
      >
        {Object.entries(options).map(([optionValue, optionLabel]) => (
          <option key={optionValue} value={optionValue}>{optionLabel}</option>
        ))}
      </select>
      {loading && <span className="text-sm text-gray-500">{loadingMessage}</span>}
    </div>
  );
}
