import type { UnitSystem, WeatherUnitConfig } from "../utils/weatherUnits.ts";

type UnitSystemToggleProps = {
  value: UnitSystem;
  unitConfig: WeatherUnitConfig;
  onChange: (unitSystem: UnitSystem) => void;
};

export function UnitSystemToggle({ value, unitConfig, onChange }: UnitSystemToggleProps) {
  return (
    <fieldset className="mb-4 flex justify-end gap-4">
      <legend className="sr-only">Mértékegység-rendszer</legend>
      {Object.entries(unitConfig.units).map(([system, units]) => (
        <label key={system} className="flex items-center gap-2">
          <input
            type="radio"
            name="unit-system"
            value={system}
            checked={value === system}
            onChange={() => onChange(system as UnitSystem)}
          />
          {units.label} ({units.temperature}, {units.precipitation})
        </label>
      ))}
    </fieldset>
  );
}
