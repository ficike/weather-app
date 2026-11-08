import { useCallback, useEffect, useState } from "react";
import { resolveAppPath, type AppPath } from "./routes.ts";

function getCurrentPath(): string {
  const pathname = resolveAppPath(window.location.pathname);
  if (pathname !== window.location.pathname) {
    window.history.replaceState({}, "", pathname);
  }
  return pathname;
}

export function useCurrentPath() {
  const [currentPath, setCurrentPath] = useState<string>(getCurrentPath);

  useEffect(() => {
    const handlePopState = () => setCurrentPath(getCurrentPath());
    window.addEventListener("popstate", handlePopState);
    return () => window.removeEventListener("popstate", handlePopState);
  }, []);

  const navigate = useCallback((path: AppPath) => {
    if (path === window.location.pathname) return;
    window.history.pushState({}, "", path);
    setCurrentPath(path);
  }, []);

  return { currentPath, navigate };
}
