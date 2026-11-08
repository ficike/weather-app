import { appRoutes, type AppPath } from "../routing/routes.ts";

type NavigationProps = {
  currentPath: string;
  onNavigate: (path: AppPath) => void;
};

export function Navigation({ currentPath, onNavigate }: NavigationProps) {
  return (
    <nav aria-label="Felületválasztó" className="px-3 py-3">
      <ul className="flex max-w-4xl gap-3">
        {appRoutes.map(({ path, label }) => (
          <li key={path}>
            <a
              href={path}
              aria-current={currentPath === path ? "page" : undefined}
              onClick={(event) => {
                event.preventDefault();
                onNavigate(path);
              }}
              className={`inline-block rounded-lg px-4 py-2 font-bold ${
                currentPath === path
                  ? "bg-amber-400"
                  : "bg-amber-100 hover:bg-amber-200"
              }`}
            >
              {label}
            </a>
          </li>
        ))}
      </ul>
    </nav>
  );
}
