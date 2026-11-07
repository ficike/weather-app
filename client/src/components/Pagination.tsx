type PaginationProps = {
  page: number;
  pageCount: number;
  onPageChange: (page: number) => void;
};

export function Pagination({ page, pageCount, onPageChange }: PaginationProps) {
  if (pageCount <= 1) return null;

  return (
    <div className="mt-4 flex items-center justify-center gap-4">
      <button
        type="button"
        onClick={() => onPageChange(Math.max(1, page - 1))}
        disabled={page === 1}
        className="rounded-lg bg-amber-300 px-4 py-2 font-bold hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50"
      >
        Előző
      </button>
      <span aria-live="polite">{page}. / {pageCount}. oldal</span>
      <button
        type="button"
        onClick={() => onPageChange(Math.min(pageCount, page + 1))}
        disabled={page === pageCount}
        className="rounded-lg bg-amber-300 px-4 py-2 font-bold hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50"
      >
        Következő
      </button>
    </div>
  );
}
