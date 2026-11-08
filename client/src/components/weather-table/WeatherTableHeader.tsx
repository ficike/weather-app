type WeatherTableHeaderProps = {
  header: Record<string, string>;
};

export function WeatherTableHeader({ header }: WeatherTableHeaderProps) {
  return (
    <thead>
      <tr className="border-b-2 border-amber-500">
        {Object.entries(header).map(([key, label]) => (
          <th key={key} scope="col" className="p-2">{label}</th>
        ))}
      </tr>
    </thead>
  );
}
