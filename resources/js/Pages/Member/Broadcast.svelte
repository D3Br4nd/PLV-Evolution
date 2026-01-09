<script>
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Button } from "@/lib/components/ui/button";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import DownloadIcon from "@tabler/icons-svelte/icons/download";
    import { Link } from "@inertiajs/svelte";

    let { broadcast } = $props();

    function formatDate(dateString) {
        if (!dateString) return "";
        return new Date(dateString).toLocaleString("it-IT", {
            day: "2-digit",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }
</script>

<MemberLayout title={broadcast.title}>
    {#snippet headerActions()}
        <Link href="/me/notifications" class="p-2">
            <ArrowLeftIcon class="size-5" />
        </Link>
    {/snippet}

    <div class="space-y-4">
        <Card.Root>
            <Card.Header class="pb-2">
                <Card.Title class="text-lg">{broadcast.title}</Card.Title>
                <Card.Description>
                    {formatDate(broadcast.created_at)}
                </Card.Description>
            </Card.Header>
            <Card.Content class="space-y-4">
                <!-- Featured Image -->
                {#if broadcast.featured_image_url}
                    <div class="-mx-4 sm:mx-0">
                        <img
                            src={broadcast.featured_image_url}
                            alt="Immagine"
                            class="w-full object-cover sm:rounded-lg"
                        />
                    </div>
                {/if}

                <!-- Content -->
                <div class="prose prose-sm max-w-none">
                    {@html broadcast.content}
                </div>

                <!-- Attachment Download -->
                {#if broadcast.attachment_url}
                    <div class="border-t pt-4">
                        <a
                            href={broadcast.attachment_url}
                            download={broadcast.attachment_name}
                            class="flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-3 text-sm font-medium text-primary-foreground"
                        >
                            <DownloadIcon class="size-5" />
                            Scarica: {broadcast.attachment_name || "Allegato"}
                        </a>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>

<style>
    :global(.prose ul) {
        list-style-type: disc;
        padding-left: 1.5rem;
    }
    :global(.prose ol) {
        list-style-type: decimal;
        padding-left: 1.5rem;
    }
    :global(.prose a) {
        color: hsl(var(--primary));
        text-decoration: underline;
    }
    :global(.prose p) {
        margin-bottom: 0.75rem;
    }
</style>
