<script lang="ts">
  import type { ButtonVariants } from "./button-variants";
  import { buttonVariants } from "./button-variants";
  import { cn } from "@/lib/utils/cn";

  type Props = ButtonVariants & {
    class?: string;
    href?: string;
    type?: "button" | "submit" | "reset";
    disabled?: boolean;
    children?: any;
    // Allow passing arbitrary attributes/events (data-*, aria-*, onclick, etc.)
    [key: string]: any;
  };

  let {
    variant,
    size,
    class: className,
    href,
    type = "button",
    disabled,
    children,
    ...rest
  } = $props() as Props;
</script>

{#if href}
  <a
    class={cn(buttonVariants({ variant, size }), className)}
    href={href}
    {...rest}
  >
    {@render children?.()}
  </a>
{:else}
  <button
    class={cn(buttonVariants({ variant, size }), className)}
    type={type}
    disabled={disabled}
    {...rest}
  >
    {@render children?.()}
  </button>
{/if}


