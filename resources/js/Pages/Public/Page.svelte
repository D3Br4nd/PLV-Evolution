<script>
    let { page } = $props();
</script>

<svelte:head>
    <title>{page.title}</title>
</svelte:head>

<div class="min-h-screen bg-background text-foreground p-6">
    <div class="max-w-3xl mx-auto space-y-4">
        <h1 class="text-3xl font-bold">{page.title}</h1>
        {#if page.excerpt}
            <p class="text-muted-foreground">{page.excerpt}</p>
        {/if}
        <div class="max-w-none whitespace-pre-wrap leading-relaxed">
            {page.body}
        </div>
    </div>
</div>


