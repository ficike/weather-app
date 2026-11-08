import { Children, isValidElement, type ReactNode } from "react";

type RouteProps = {
  path: string;
  element: ReactNode;
};

type RoutesProps = {
  path: string;
  children: ReactNode;
  notFound: ReactNode;
};

export function Route(_props: RouteProps) {
  return null;
}

export function Routes({ path, children, notFound }: RoutesProps) {
  let matchedElement: ReactNode = notFound;

  Children.forEach(children, (child) => {
    if (isValidElement<RouteProps>(child) && child.type === Route && child.props.path === path) {
      matchedElement = child.props.element;
    }
  });

  return <>{matchedElement}</>;
}
