<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import { Badge } from "@/lib/components/ui/badge";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import DownloadIcon from "@tabler/icons-svelte/icons/download";
    import ImageIcon from "@tabler/icons-svelte/icons/photo";

    let { broadcast } = $props();

    function formatDate(dateString) {
        if (!dateString) return "-";
        return new Date(dateString).toLocaleString("it-IT", {
            day: "2-digit",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }
</script>

<AdminLayout title={broadcast.title}>
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <Button variant="ghost" size="icon" href="/admin/broadcasts">
                <ArrowLeftIcon class="size-5" />
            </Button>
            <div class="flex-1">
                <h1 class="text-2xl font-bold tracking-tight">
                    {broadcast.title}
                </h1>
                <div
                    class="mt-1 flex items-center gap-3 text-sm text-muted-foreground"
                >
                    {#if broadcast.created_by}
                        <span>Creata da {broadcast.created_by.name}</span>
                    {/if}
                    <span>•</span>
                    <span>{formatDate(broadcast.created_at)}</span>
                </div>
            </div>
            {#if broadcast.sent_at}
                <Badge variant="default"
                    >Inviata il {formatDate(broadcast.sent_at)}</Badge
                >
            {:else}
                <Badge variant="secondary">Bozza</Badge>
            {/if}
        </div>

        <Card.Root>
            <Card.Content class="p-6 space-y-6">
                <!-- Featured Image -->
                {#if broadcast.featured_image_url}
                    <div>
                        <img
                            src={broadcast.featured_image_url}
                            alt="Immagine in evidenza"
                            class="max-h-64 rounded-lg border object-cover"
                        />
                    </div>
                {/if}

                <!-- Content -->
                <div class="prose prose-sm max-w-none">
                    {@html broadcast.content}
                </div>

                <!-- Attachment -->
                {#if broadcast.attachment_url}
                    <div class="border-t pt-4">
                        <p
                            class="mb-2 text-sm font-medium text-muted-foreground"
                        >
                            Allegato
                        </p>
                        <a
                            href={broadcast.attachment_url}
                            download={broadcast.attachment_name}
                            class="inline-flex items-center gap-2 rounded-lg border bg-muted/30 px-4 py-2 text-sm hover:bg-muted/50 transition-colors"
                        >
                            <DownloadIcon class="size-4" />
                            {broadcast.attachment_name || "Scarica allegato"}
                        </a>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>
</AdminLayout>
