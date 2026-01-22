<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Button } from "@/lib/components/ui/button";
    import { Badge } from "@/lib/components/ui/badge";
    import {
        Avatar,
        AvatarFallback,
        AvatarImage,
    } from "@/lib/components/ui/avatar";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import EditIcon from "@tabler/icons-svelte/icons/edit";
    import CalendarIcon from "@tabler/icons-svelte/icons/calendar";
    import { Link } from "@inertiajs/svelte";

    let { project } = $props();

    /** @param {string} dateString */
    function formatDate(dateString) {
        if (!dateString) return "Nessuna scadenza";
        return new Date(dateString).toLocaleDateString("it-IT", {
            day: "2-digit",
            month: "long",
            year: "numeric",
        });
    }

    /** @param {string} s */
    function statusLabel(s) {
        /** @type {Record<string, string>} */
        const labels = {
            todo: "Da fare",
            in_progress: "In corso",
            done: "Completato",
        };
        return labels[s] || s;
    }

    /** @param {string} p */
    function priorityLabel(p) {
        /** @type {Record<string, string>} */
        const labels = {
            low: "Bassa",
            medium: "Media",
            high: "Alta",
        };
        return labels[p] || p;
    }

    /** @param {string} s */
    function statusColor(s) {
        if (s === "done")
            return "bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400";
        if (s === "in_progress")
            return "bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400";
        return "bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400";
    }

    /** @param {string} p */
    function priorityColor(p) {
        if (p === "high") return "destructive";
        if (p === "medium") return "default";
        return "secondary";
    }
</script>

{#snippet headerActions()}
    <Button
        variant="ghost"
        size="icon"
        href="/admin/projects"
        onclick={undefined}
    >
        <ArrowLeftIcon class="size-5" />
    </Button>
{/snippet}

<AdminLayout title={project.title} {headerActions}>
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <Badge
                        variant="outline"
                        class={statusColor(project.status)}
                        href={undefined}
                    >
                        {statusLabel(project.status)}
                    </Badge>
                    <Badge
                        variant={priorityColor(project.priority)}
                        href={undefined}
                    >
                        Priorità {priorityLabel(project.priority)}
                    </Badge>
                </div>
                <h1 class="text-3xl font-bold tracking-tight">
                    {project.title}
                </h1>
            </div>
            <div class="flex gap-2">
                <Button
                    variant="default"
                    href="/admin/projects"
                    onclick={undefined}
                >
                    <EditIcon class="size-4 mr-2" />
                    Gestisci (Board)
                </Button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <Card.Root class="md:col-span-2">
                <Card.Header>
                    <Card.Title>Descrizione</Card.Title>
                </Card.Header>
                <Card.Content class="space-y-6">
                    <div class="prose prose-sm max-w-none">
                        {#if project.description}
                            <div class="text-muted-foreground leading-relaxed">
                                {project.description}
                            </div>
                        {:else}
                            <p class="text-muted-foreground italic">
                                Nessuna descrizione breve.
                            </p>
                        {/if}
                    </div>

                    {#if project.content}
                        <div class="pt-6 border-t font-medium text-sm mb-2">
                            Contenuto Dettagliato
                        </div>
                        <div
                            class="prose prose-sm max-w-none text-foreground/80 leading-relaxed whitespace-pre-wrap"
                        >
                            {project.content}
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>

            <div class="space-y-6">
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Scadenza & Comitato</Card.Title>
                    </Card.Header>
                    <Card.Content class="space-y-4">
                        <div class="flex items-center gap-2">
                            <CalendarIcon
                                class="size-4 text-muted-foreground"
                            />
                            <span class="text-sm"
                                >{formatDate(project.deadline)}</span
                            >
                        </div>
                        {#if project.committee}
                            <div class="pt-2 border-t">
                                <div
                                    class="text-xs font-bold text-muted-foreground uppercase mb-1"
                                >
                                    Comitato
                                </div>
                                <Link
                                    href={`/admin/committees/${project.committee.id}`}
                                    class="text-sm font-medium hover:underline"
                                >
                                    {project.committee.name}
                                </Link>
                            </div>
                        {/if}
                    </Card.Content>
                </Card.Root>

                <Card.Root>
                    <Card.Header>
                        <Card.Title>Team</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <div class="space-y-3">
                            {#if project.members && project.members.length}
                                {#each project.members as member}
                                    <div class="flex items-center gap-2">
                                        <Avatar class="size-6">
                                            <AvatarImage
                                                src={member.avatar_url}
                                            />
                                            <AvatarFallback
                                                >{member.name.charAt(
                                                    0,
                                                )}</AvatarFallback
                                            >
                                        </Avatar>
                                        <span class="text-sm"
                                            >{member.name}</span
                                        >
                                    </div>
                                {/each}
                            {:else}
                                <p class="text-xs text-muted-foreground italic">
                                    Nessun membro assegnato.
                                </p>
                            {/if}
                        </div>
                    </Card.Content>
                </Card.Root>
            </div>
        </div>
    </div>
</AdminLayout>
