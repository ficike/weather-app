import { createContext, useContext, useState, type ReactNode } from "react";
import type { UnitSystem } from "../utils/weatherUnits.ts";

type UnitSystemContextValue = {
  unitSystem: UnitSystem;
  setUnitSystem: (unitSystem: UnitSystem) => void;
};

const UnitSystemContext = createContext<UnitSystemContextValue | null>(null);

type UnitSystemProviderProps = {
  children: ReactNode;
};

export function UnitSystemProvider({ children }: UnitSystemProviderProps) {
  const [unitSystem, setUnitSystem] = useState<UnitSystem>("si");

  return (
    <UnitSystemContext.Provider value={{ unitSystem, setUnitSystem }}>
      {children}
    </UnitSystemContext.Provider>
  );
}

export function useUnitSystem() {
  const context = useContext(UnitSystemContext);
  if (!context) {
    throw new Error("useUnitSystem must be used within UnitSystemProvider");
  }
  return context;
}
