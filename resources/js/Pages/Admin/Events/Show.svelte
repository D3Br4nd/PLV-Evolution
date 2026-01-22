<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Button } from "@/lib/components/ui/button";
    import { Badge } from "@/lib/components/ui/badge";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import EditIcon from "@tabler/icons-svelte/icons/edit";
    import UsersIcon from "@tabler/icons-svelte/icons/users";
    import { Link } from "@inertiajs/svelte";

    let { event } = $props();

    /** @param {string} iso */
    function fmtDateTime(iso) {
        if (!iso) return "-";
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return "-";
        return d.toLocaleString("it-IT", {
            weekday: "long",
            day: "2-digit",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    /** @param {string} t */
    function typeLabel(t) {
        /** @type {Record<string, string>} */
        const labels = {
            fair: "Fiera",
            festival: "Sagra",
            meeting: "Riunione",
            event: "Evento",
            other: "Altro",
        };
        return labels[t] || t || "-";
    }
</script>

{#snippet headerActions()}
    <Button
        variant="ghost"
        size="icon"
        href="/admin/events"
        onclick={undefined}
    >
        <ArrowLeftIcon class="size-5" />
    </Button>
{/snippet}

<AdminLayout title={event.title} {headerActions}>
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <Badge
                        variant={event.type === "festival"
                            ? "secondary"
                            : event.type === "meeting"
                              ? "outline"
                              : "default"}
                        href={undefined}
                    >
                        {typeLabel(event.type)}
                    </Badge>
                    {#if event.committee}
                        <Badge variant="secondary" href={undefined}
                            >Comitato: {event.committee.name}</Badge
                        >
                    {/if}
                </div>
                <h1 class="text-3xl font-bold tracking-tight">{event.title}</h1>
            </div>
            <div class="flex gap-2">
                <Button
                    variant="outline"
                    href={`/admin/events/${event.id}/checkins`}
                    onclick={undefined}
                >
                    <UsersIcon class="size-4 mr-2" />
                    Check-ins
                </Button>
                <!-- Redirect to calendar and ideally we'd trigger the edit modal, but for now just back to list is safe -->
                <Button
                    variant="default"
                    href="/admin/events"
                    onclick={undefined}
                >
                    <EditIcon class="size-4 mr-2" />
                    Modifica (Calendario)
                </Button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <Card.Root class="md:col-span-2">
                <Card.Header>
                    <Card.Title>Descrizione</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="prose prose-sm max-w-none">
                        {#if event.description}
                            <div
                                class="whitespace-pre-wrap text-sm text-foreground/80 leading-relaxed"
                            >
                                {event.description}
                            </div>
                        {:else}
                            <p class="text-muted-foreground italic">
                                Nessuna descrizione disponibile.
                            </p>
                        {/if}
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Header>
                    <Card.Title>Informazioni</Card.Title>
                </Card.Header>
                <Card.Content class="space-y-4">
                    <div>
                        <div
                            class="text-xs font-bold text-muted-foreground uppercase mb-1"
                        >
                            Inizio
                        </div>
                        <div class="text-sm">
                            {fmtDateTime(event.start_date)}
                        </div>
                    </div>
                    <div>
                        <div
                            class="text-xs font-bold text-muted-foreground uppercase mb-1"
                        >
                            Fine
                        </div>
                        <div class="text-sm">{fmtDateTime(event.end_date)}</div>
                    </div>
                    {#if event.committee}
                        <div class="pt-2 border-t">
                            <div
                                class="text-xs font-bold text-muted-foreground uppercase mb-1"
                            >
                                Comitato Organizzatore
                            </div>
                            <Link
                                href={`/admin/committees/${event.committee.id}`}
                                class="text-sm font-medium hover:underline"
                            >
                                {event.committee.name}
                            </Link>
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>
        </div>
    </div>
</AdminLayout>
