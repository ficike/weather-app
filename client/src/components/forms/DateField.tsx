type DateFieldProps = {
  id: string;
  label: string;
  value: string;
  onChange: (date: string) => void;
};

export function DateField({ id, label, value, onChange }: DateFieldProps) {
  return (
    <div className="flex flex-col gap-2">
      <label htmlFor={id} className="font-bold">{label}</label>
      <input
        id={id}
        type="date"
        value={value}
        onChange={(event) => onChange(event.target.value)}
        className="rounded-md bg-gray-100 p-2"
      />
    </div>
  );
}
