export default {
    autocomplete: {
        root: ({ props }) => ({
            class: [
                'relative',
                // Flex
                {
                    flex: props.fluid,
                    'inline-flex': !props.fluid,
                },
                // Size
                { 'w-full': props.multiple },
                // Color
                'text-gray-900',
                //States
                {
                    'bg-gray-200 select-none pointer-events-none cursor-default': props.disabled,
                },
            ],
        }),
        // py-2 px-3 text-gray-800 dark:text-white/80 placeholder:text-gray-400 dark:placeholder:text-gray-500 bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-700 invalid:focus:ring-red-200 invalid:hover:border-red-500 hover:border-gray-400 dark:hover:border-gray-600 focus:outline-none focus:outline-offset-0 focus:ring-1 focus:ring-primary-500 dark:focus:ring-primary-400 focus:z-10 appearance-none transition-colors duration-200
        inputMultiple: ({ props, state }) => ({
            class: [
                // Font
                'leading-none',
                // Flex
                'flex items-center flex-wrap',
                'gap-2',
                // Spacing
                'm-0 list-none',
                'py-1 px-1',
                // Size
                'w-full',
                // Shape
                'appearance-none rounded-md',
                // Color
                'text-gray-700',
                'placeholder:text-gray-400',
                { 'bg-white': !props.disabled },
                'border',
                { 'border-gray-300': !props.invalid },
                // Invalid State
                'invalid:focus:ring-red-200',
                'invalid:hover:border-red-500',
                { 'border-red-500': props.invalid },
                // States
                { 'hover:border-gray-400': !props.invalid },
                { 'outline-none outline-offset-0 z-10 ring-1 ring-primary-500': state.focused },
                // Transition
                'transition duration-200 ease-in-out',
                // Misc
                'cursor-text overflow-hidden',
            ],
        }),
        inputToken: {
            class: ['py-1 px-0 ml-2', 'inline-flex flex-auto'],
        },
        inputChip: {
            class: 'flex-auto inline-flex pt-1 pb-1',
        },
        input: {
            class: 'border-none outline-none bg-transparent m-0 p-0 shadow-none rounded-none w-full',
        },
        dropdown: {
            class: [
                'relative',
                'items-center inline-flex justify-center text-center align-bottom',
                'rounded-r-md',
                'py-2 leading-none',
                'w-10',
                'text-primary-contrast',
                'bg-primary',
                'border border-primary',
                'focus:outline-none focus:outline-offset-0 focus:ring-1 ',
                'hover:bg-primary-emphasis hover:border-primary-emphasis',
                'focus:ring-primary-500',
            ],
        },
        loader: {
            class: ['text-gray-500', 'absolute top-[50%] right-[0.5rem] -mt-2 animate-spin'],
        },
        overlay: {
            class: ['bg-white', 'text-gray-700', 'border border-gray-300', 'rounded-md', 'shadow-md', 'overflow-auto'],
        },
        list: {
            class: 'p-1 list-none m-0',
        },
        option: ({ context }) => ({
            class: [
                'relative',
                // Font
                'leading-none',
                // Spacing
                'm-0 px-3 py-2',
                'first:mt-0 mt-[2px]',
                // Shape
                'border-0 rounded',
                // Colors
                {
                    'text-gray-700': !context.focused && !context.selected,
                    'bg-gray-200': context.focused && !context.selected,
                    'text-gray-700': context.focused && !context.selected,
                    'bg-highlight': context.selected,
                },
                //States
                {
                    'hover:bg-gray-100': !context.focused && !context.selected,
                },
                { 'hover:bg-highlight-emphasis': context.selected },
                {
                    'hover:text-gray-700 hover:bg-gray-100': context.focused && !context.selected,
                },
                // Transition
                'transition-shadow duration-200',
                // Misc
                'cursor-pointer overflow-hidden whitespace-nowrap',
            ],
        }),
        optionGroup: {
            class: ['font-semibold', 'm-0 py-2 px-3', 'text-gray-400', 'cursor-auto'],
        },
        emptyMessage: {
            class: ['leading-none', 'py-2 px-3', 'text-gray-800', 'bg-transparent'],
        },
        transition: {
            enterFromClass: 'opacity-0 scale-y-[0.8]',
            enterActiveClass: 'transition-[transform,opacity] duration-[120ms] ease-[cubic-bezier(0,0,0.2,1)]',
            leaveActiveClass: 'transition-opacity duration-100 ease-linear',
            leaveToClass: 'opacity-0',
        },
    },
    checkbox: {
        root: {
            class: ['relative', 'inline-flex', 'align-bottom', 'w-5', 'h-5', 'select-none'],
        },
        box: ({ props, context }) => ({
            class: [
                // 'hidden',
                'flex',
                'items-center',
                'justify-center',
                'w-4',
                'h-4',
                'my-0.5',
                'ring-1',
                // Colors
                {
                    'ring-gray-300': !context.checked && !props.invalid,
                    'bg-white': !context.checked && !props.invalid && !props.disabled,
                    'ring-primary-700 bg-primary-700 text-white': context.checked,
                },
                // Invalid State
                'invalid:focus:ring-red-200',
                { 'ring-red-500': props.invalid },
                // States
                {
                    'peer-focus-visible:z-10 peer-focus-visible:outline-none peer-focus-visible:outline-offset-0 peer-focus-visible:ring-1 peer-focus-visible:ring-primary-500':
                        !props.disabled,
                    'bg-gray-200 select-none pointer-events-none cursor-default': props.disabled,
                },
            ],
        }),
        input: ({ props, context }) => ({
            class: [
                'peer',
                'w-full ',
                'h-full',
                'absolute',
                'top-0 left-0',
                'z-10',
                'p-0',
                'm-0',
                'opacity-0',
                'rounded',
                'outline-none',
                'border border-gray-300',
                'appearance-none',
                {
                    'cursor-default': props.disabled,
                    'cursor-pointer': !props.disabled,
                },
            ],
        }),
        icon: ({ context, state }) => ({
            class: [
                'w-[0.75rem]',
                'h-[0.75rem]',
                {
                    'text-white': context.checked,
                    'text-primary-700': state.d_indeterminate,
                },
            ],
        }),
    },

    inputtext: {
        root: ({ props, context, parent }) => ({
            class: [
                // Font
                'text-sm leading-normal',
                // Flex
                { 'flex-1 w-[1%]': parent.instance.$name == 'InputGroup' },
                // Spacing
                'm-0',
                { 'w-full': props.fluid },
                // Size
                {
                    'py-3 px-3.5': props.size == 'large',
                    'py-1.5 px-2': props.size == 'small',
                    'py-2 px-3': props.size == null,
                },
                // Shape
                { 'rounded-md': parent.instance.$name !== 'InputGroup' },
                { 'first:rounded-l-md rounded-none last:rounded-r-md': parent.instance.$name == 'InputGroup' },
                { 'border-0 border-y border-l last:border-r': parent.instance.$name == 'InputGroup' },
                { 'first:ml-0 -ml-px': parent.instance.$name == 'InputGroup' && !props.showButtons },
                // Colors
                'text-gray-800',
                'placeholder:text-gray-400',
                { 'bg-white': !context.disabled },
                'border',
                { 'border-gray-300': !props.invalid },
                // Invalid State
                'invalid:focus:ring-red-200',
                'invalid:hover:border-red-500',
                { 'border-red-500': props.invalid },
                // States
                {
                    'hover:border-gray-400': !context.disabled && !props.invalid,
                    'focus:outline-none focus:outline-offset-0 focus:ring-1 focus:ring-primary-500 focus:z-10':
                        !context.disabled,
                    'bg-gray-200 select-none pointer-events-none cursor-default': context.disabled,
                },
                // Filled State *for FloatLabel
                {
                    filled:
                        (parent.instance === null ? void 0 : parent.instance.$name) === 'FloatLabel' && context.filled,
                },
                // Misc
                'appearance-none',
                'transition-colors duration-200',
            ],
        }),
    },

    select: {
        root: ({ props, state, parent }) => ({
            class: [
                // Display and Position
                'inline-flex',
                'relative',
                // Shape
                { 'rounded-md': parent.instance.$name !== 'InputGroup' },
                { 'first:rounded-l-md rounded-none last:rounded-r-md': parent.instance.$name == 'InputGroup' },
                { 'border-0 border-y border-l last:border-r': parent.instance.$name == 'InputGroup' },
                { 'first:ml-0 ml-[-1px]': parent.instance.$name == 'InputGroup' && !props.showButtons },
                // Color and Background
                { 'bg-white': !props.disabled },
                'border',
                { 'border-gray-300': !props.invalid },
                // Invalid State
                'invalid:focus:ring-red-200',
                'invalid:hover:border-red-500',
                { 'border-red-500': props.invalid },
                // Transitions
                'transition-all',
                'duration-200',
                // States
                { 'hover:border-gray-400': !props.invalid },
                { 'outline-none outline-offset-0 ring-1 ring-primary-500 z-10': state.focused },
                // Misc
                'cursor-pointer',
                'select-none',
                { 'bg-gray-200 select-none pointer-events-none cursor-default': props.disabled },
            ],
        }),
        label: ({ props, parent }) => {
            var _a;
            return {
                class: [
                    //Font
                    'text-sm leading-normal',
                    // Display
                    'block',
                    'flex-auto',
                    // Color and Background
                    'bg-transparent',
                    'border-0',
                    {
                        'text-gray-800': props.modelValue != null,
                        'text-gray-400': props.modelValue == null,
                    },
                    'placeholder:text-gray-400',
                    // Sizing and Spacing
                    'w-[1%]',
                    'py-2 px-3',
                    { 'pr-7': props.showClear },
                    //Shape
                    'rounded-none',
                    // Transitions
                    'transition',
                    'duration-200',
                    // States
                    'focus:outline-none focus:shadow-none',
                    // Filled State *for FloatLabel
                    {
                        filled:
                            ((_a = parent.instance) == null ? void 0 : _a.$name) == 'FloatLabel' &&
                            props.modelValue !== null,
                    },
                    // Misc
                    'relative',
                    'cursor-pointer',
                    'overflow-hidden overflow-ellipsis',
                    'whitespace-nowrap',
                    'appearance-none',
                ],
            };
        },
        dropdown: {
            class: [
                'flex items-center justify-center',
                'shrink-0',
                'bg-transparent',
                'text-gray-500',
                'w-12',
                'rounded-r-md',
            ],
        },
        overlay: {
            class: ['bg-white', 'text-gray-700', 'border border-gray-300', 'rounded-md', 'shadow-md'],
        },
        listContainer: {
            class: ['max-h-[200px]', 'overflow-auto'],
        },
        list: {
            class: 'p-1 list-none m-0 bg-white',
        },
        option: ({ context }) => ({
            class: [
                'relative',
                'flex items-center',
                // Font
                'text-sm leading-none',
                // Spacing
                'm-0 px-3 py-2',
                'first:mt-0 mt-[2px]',
                // Shape
                'border-0 rounded',
                // Colors
                {
                    'text-gray-700': !context.focused && !context.selected,
                    'bg-gray-200': context.focused && !context.selected,
                    'text-gray-700': context.focused && !context.selected,
                    'bg-highlight': context.selected,
                },
                //States
                {
                    'hover:bg-gray-100': !context.focused && !context.selected,
                },
                { 'hover:bg-highlight-emphasis': context.selected },
                {
                    'hover:text-gray-700 hover:bg-gray-100': context.focused && !context.selected,
                },
                // Transition
                'transition-shadow duration-200',
                // Misc
                'cursor-pointer overflow-hidden whitespace-nowrap',
            ],
        }),
        optionGroup: {
            class: ['font-semibold', 'm-0 py-2 px-3', 'text-gray-400', 'cursor-auto'],
        },
        optionCheckIcon: 'relative -ms-1.5 me-1.5 text-gray-700 w-4 h-4',
        optionBlankIcon: 'w-4 h-4',
        emptyMessage: {
            class: ['leading-none', 'py-2 px-3', 'text-gray-800', 'bg-transparent'],
        },
        header: {
            class: [
                'pt-2 px-2 pb-0',
                'm-0',
                'border-b-0',
                'rounded-tl-md',
                'rounded-tr-md',
                'text-gray-700',
                'bg-white',
                'border-gray-300',
            ],
        },
        clearIcon: {
            class: ['text-gray-400', 'absolute', 'top-1/2', 'right-12', '-mt-2'],
        },
        loadingIcon: {
            class: 'text-gray-400 animate-spin',
        },
        transition: {
            enterFromClass: 'opacity-0 scale-y-[0.8]',
            enterActiveClass: 'transition-[transform,opacity] duration-[120ms] ease-[cubic-bezier(0,0,0.2,1)]',
            leaveActiveClass: 'transition-opacity duration-100 ease-linear',
            leaveToClass: 'opacity-0',
        },
    },
    tree: {
        root: {
            class: ['[&_[data-pc-name=pcfilter]]:w-full'],
        },

        node: {
            class: [
                'focus:outline-none focus:outline-offset-0 focus:ring-1 focus:ring-inset focus:ring-primary-500 focus:z-10',
            ],
        },
        nodeContent: ({ context, props }) => ({
            class: [
                // Flex and Alignment
                'flex items-center',
                // Spacing
                'gap-2',
                // Colors
                context.selected ? 'bg-highlight text-primary-700' : 'bg-transparent text-gray-600',
                // States
                {
                    'hover:bg-gray-50':
                        (props.selectionMode == 'single' || props.selectionMode == 'multiple') && !context.selected,
                },
                // Transition
                'transition-shadow duration-200',
                { 'cursor-pointer select-none': props.selectionMode == 'single' || props.selectionMode == 'multiple' },
            ],
        }),
        nodeToggleButton: ({ context }) => ({
            class: [
                'order-last',
                // Flex and Alignment
                'inline-flex items-center justify-center',
                // Shape
                'border-0 rounded-full',
                // Size
                'w-7 h-7',
                // Colors
                'bg-transparent',
                {
                    'text-gray-600': !context.selected,
                    'text-primary-700': context.selected,
                    invisible: context.leaf,
                },
                // States
                'hover:bg-gray-200/20',
                'focus:outline-none focus:outline-offset-0 focus:ring-1 focus:ring-primary-500',
                // Transition
                'transition duration-200',
                // Misc
                'cursor-pointer select-none',
            ],
        }),
        nodeIcon: ({ context }) => ({
            class: [
                'empty:hidden',
                // Space
                'mr-2',
                // Color
                {
                    'text-gray-600': !context.selected,
                    'text-primary-700': context.selected,
                },
            ],
        }),
        nodeLabel: {
            class: ['flex-1', 'text-sm font-medium', 'text-gray-700'],
        },
        nodeChildren: {
            class: ['m-0 list-none p-0 pl-4 [&:not(ul)]:pl-0 [&:not(ul)]:my-[2px]'],
        },
        loadingIcon: {
            class: ['text-gray-500', 'absolute top-[50%] right-[50%] -mt-2 -mr-2 animate-spin'],
        },
    },
};
