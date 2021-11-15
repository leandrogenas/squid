import { Props } from "@blueprintjs/core";

export interface IPropsContexto<T = {}> extends Props {
    /**
     * Identifier of this example.
     * This will appear as the `data-example-id` attribute on the DOM element.
     */
    id: string;

    /**
     * Container for arbitary data passed to each example from the parent
     * application. This prop is ignored by the `<Example>` component; it is
     * available for your example implementations to access by providing a `<T>`
     * type to this interface. Pass actual `data` when defining your example map
     * for the `ReactExampleTagRenderer`.
     *
     * A container like this is necessary because unknown props on the
     * `<Example>` component are passed to its underlying DOM element, so adding
     * your own props will result in React "unknown prop" warnings.
     */
    data?: T;
}
