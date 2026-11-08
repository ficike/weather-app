type NotFoundProps = {
  onNavigateHome: () => void;
};

export function NotFound({ onNavigateHome }: NotFoundProps) {
  return (
    <main className="mx-auto max-w-4xl px-4 py-8">
      <h1 className="text-2xl font-bold">404 – Az oldal nem található</h1>
      <p className="mt-2">A keresett oldal nem létezik.</p>
      <button
        type="button"
        onClick={onNavigateHome}
        className="mt-4 rounded-lg bg-amber-300 px-4 py-2 font-bold hover:bg-amber-400"
      >
        Vissza a városi időjáráshoz
      </button>
    </main>
  );
}
