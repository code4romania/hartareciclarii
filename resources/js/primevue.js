export default {
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
