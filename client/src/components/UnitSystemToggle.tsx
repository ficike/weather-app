import { useUnitSystem } from "../contexts/UnitSystemContext.tsx";
import { unitSystems, type WeatherUnitConfig } from "../utils/weatherUnits.ts";

type UnitSystemToggleProps = {
  unitConfig: WeatherUnitConfig;
};

export function UnitSystemToggle({ unitConfig }: UnitSystemToggleProps) {
  const { unitSystem, setUnitSystem } = useUnitSystem();

  return (
    <fieldset className="mb-4 flex justify-end gap-4">
      <legend className="sr-only">Mértékegység-rendszer</legend>
      {unitSystems.map((system) => {
        const units = unitConfig.units[system];
        return (
          <label key={system} className="flex items-center gap-2">
            <input
              type="radio"
              name="unit-system"
              value={system}
              checked={unitSystem === system}
              onChange={() => setUnitSystem(system)}
            />
            {units.label} ({units.temperature}, {units.precipitation})
          </label>
        );
      })}
    </fieldset>
  );
}
