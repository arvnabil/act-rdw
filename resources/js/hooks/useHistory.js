import { useState, useCallback } from "react";

/**
 * useHistory Hook
 * Manages state with past, present, and future stacks for Undo/Redo.
 *
 * @param {any} initialPresent - Initial state value
 * @param {number} limit - Max number of history steps to keep (to prevent memory leaks)
 */
export default function useHistory(initialPresent, limit = 50) {
    const [state, setState] = useState({
        past: [],
        present: initialPresent,
        future: [],
    });

    const canUndo = state.past.length > 0;
    const canRedo = state.future.length > 0;

    /**
     * Undo action: moves present to future, pop from past to present
     */
    const undo = useCallback(() => {
        setState((currentState) => {
            if (currentState.past.length === 0) return currentState;

            const previous = currentState.past[currentState.past.length - 1];
            const newPast = currentState.past.slice(0, -1);

            return {
                past: newPast,
                present: previous,
                future: [currentState.present, ...currentState.future],
            };
        });
    }, []);

    /**
     * Redo action: moves present to past, shift from future to present
     */
    const redo = useCallback(() => {
        setState((currentState) => {
            if (currentState.future.length === 0) return currentState;

            const next = currentState.future[0];
            const newFuture = currentState.future.slice(1);

            return {
                past: [...currentState.past, currentState.present],
                present: next,
                future: newFuture,
            };
        });
    }, []);

    /**
     * Set new state: pushes current present to past, clears future
     * @param {any|function} newPresent - New value or function(currentValue)
     */
    const set = useCallback(
        (newPresent) => {
            setState((currentState) => {
                const nextValue =
                    typeof newPresent === "function"
                        ? newPresent(currentState.present)
                        : newPresent;

                if (
                    JSON.stringify(nextValue) ===
                    JSON.stringify(currentState.present)
                ) {
                    return currentState; // No change
                }

                const newPast = [...currentState.past, currentState.present];
                if (newPast.length > limit) {
                    newPast.shift(); // Remove oldest
                }

                return {
                    past: newPast,
                    present: nextValue,
                    future: [],
                };
            });
        },
        [limit],
    );

    return {
        state: state.present,
        set,
        undo,
        redo,
        canUndo,
        canRedo,
        historyState: state, // Access complete state if needed
    };
}
