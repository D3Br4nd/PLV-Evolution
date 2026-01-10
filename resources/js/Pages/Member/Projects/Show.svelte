<script>
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Badge } from "@/lib/components/ui/badge";
    import * as Card from "@/lib/components/ui/card";
    import { ArrowLeft, Calendar, Users } from "lucide-svelte";
    import { toast } from "svelte-sonner";

    let { project } = $props();

    let processing = $state(false);

    function statusLabel(status) {
        return (
            {
                todo: "Da Fare",
                in_progress: "In Corso",
                done: "Completato",
            }[status] || status
        );
    }

    function changeStatus(newStatus) {
        if (newStatus === project.status) return;

        processing = true;
        router.patch(
            `/me/projects/${project.id}`,
            {
                status: newStatus,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("Stato aggiornato con successo");
                },
                onError: () => {
                    toast.error("Errore durante l'aggiornamento dello stato");
                },
                onFinish: () => {
                    processing = false;
                },
            },
        );
    }
</script>

<MemberLayout title={project.title}>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <Button
                variant="ghost"
                size="icon"
                onclick={() => router.visit("/me/projects")}
            >
                <ArrowLeft class="size-5" />
            </Button>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-bold tracking-tight truncate">
                    {project.title}
                </h1>
            </div>
        </div>

        <!-- Status Selector Card -->
        <Card.Root>
            <Card.Header>
                <Card.Title class="text-base">Stato Progetto</Card.Title>
                <Card.Description>Cambia lo stato del task</Card.Description>
            </Card.Header>
            <Card.Content class="space-y-2">
                {#each ["todo", "in_progress", "done"] as status}
                    <button
                        onclick={() => changeStatus(status)}
                        disabled={processing}
                        class={[
                            "w-full rounded-lg border p-3 text-left transition-all",
                            project.status === status
                                ? "bg-primary text-primary-foreground border-primary ring-2 ring-primary/20"
                                : "bg-card hover:bg-muted/50",
                            processing
                                ? "opacity-50 cursor-not-allowed"
                                : "cursor-pointer",
                        ].join(" ")}
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm"
                                >{statusLabel(status)}</span
                            >
                            {#if project.status === status}
                                <Badge
                                    variant="secondary"
                                    class="text-[9px] h-4 px-1.5">Attivo</Badge
                                >
                            {/if}
                        </div>
                    </button>
                {/each}
            </Card.Content>
        </Card.Root>

        <!-- Project Details -->
        <Card.Root>
            <Card.Header>
                <Card.Title class="text-base">Dettagli</Card.Title>
            </Card.Header>
            <Card.Content class="space-y-4">
                {#if project.committee}
                    <div class="flex items-center gap-2 text-sm">
                        <Users class="size-4 text-muted-foreground" />
                        <span class="text-muted-foreground">Comitato:</span>
                        <span class="font-medium">{project.committee.name}</span
                        >
                    </div>
                {/if}

                {#if project.deadline}
                    <div class="flex items-center gap-2 text-sm">
                        <Calendar class="size-4 text-muted-foreground" />
                        <span class="text-muted-foreground">Scadenza:</span>
                        <span class="font-medium"
                            >{new Date(project.deadline).toLocaleDateString(
                                "it-IT",
                                {
                                    day: "2-digit",
                                    month: "long",
                                    year: "numeric",
                                },
                            )}</span
                        >
                    </div>
                {/if}

                {#if project.content}
                    <div class="pt-3 border-t">
                        <h4
                            class="text-sm font-semibold mb-2 text-muted-foreground uppercase tracking-wider"
                        >
                            Descrizione
                        </h4>
                        <div
                            class="prose prose-sm max-w-none dark:prose-invert"
                        >
                            {@html project.content}
                        </div>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>

        {#if project.members?.length}
            <Card.Root>
                <Card.Header>
                    <Card.Title class="text-base">Membri Assegnati</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div class="space-y-2">
                        {#each project.members as member}
                            <div
                                class="flex items-center gap-3 p-2 rounded-lg bg-muted/30"
                            >
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-bold"
                                >
                                    {member.name.charAt(0)}
                                </div>
                                <span class="text-sm font-medium"
                                    >{member.name}</span
                                >
                            </div>
                        {/each}
                    </div>
                </Card.Content>
            </Card.Root>
        {/if}
    </div>
</MemberLayout>
