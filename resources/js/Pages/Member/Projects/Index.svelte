<script>
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Badge } from "@/lib/components/ui/badge";
    import { cn } from "@/lib/utils/cn";
    import { Calendar, CheckCircle2, Circle, Clock } from "lucide-svelte";

    let { projects } = $props();

    function statusLabel(status) {
        return (
            {
                todo: "Da Fare",
                in_progress: "In Corso",
                done: "Completato",
            }[status] || status
        );
    }

    function statusIcon(status) {
        return (
            {
                todo: Circle,
                in_progress: Clock,
                done: CheckCircle2,
            }[status] || Circle
        );
    }

    function statusColor(status) {
        return (
            {
                todo: "text-muted-foreground",
                in_progress: "text-blue-600",
                done: "text-emerald-600",
            }[status] || "text-muted-foreground"
        );
    }
</script>

<MemberLayout title="Progetti">
    <div class="space-y-4">
        <div class="rounded-xl border bg-card p-4">
            <h1 class="text-xl font-bold tracking-tight">I miei progetti</h1>
            <p class="text-sm text-muted-foreground mt-1">
                Task assegnati a te e il loro stato attuale
            </p>
        </div>

        {#if projects.length}
            <div class="space-y-3">
                {#each projects as project (project.id)}
                    {@const Icon = statusIcon(project.status)}
                    <Link
                        href="/me/projects/{project.id}"
                        class="block rounded-xl border bg-card p-4 hover:bg-muted/30 transition-colors"
                    >
                        <div
                            class="flex items-start justify-between gap-3 mb-2"
                        >
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <Icon
                                    class={cn(
                                        "size-4 shrink-0",
                                        statusColor(project.status),
                                    )}
                                />
                                <h3
                                    class="font-semibold text-sm leading-tight truncate"
                                >
                                    {project.title}
                                </h3>
                            </div>
                            <Badge
                                variant="secondary"
                                class="text-[9px] h-5 px-2 shrink-0 uppercase"
                            >
                                {statusLabel(project.status)}
                            </Badge>
                        </div>

                        {#if project.committee}
                            <div
                                class="text-xs text-muted-foreground mb-2 truncate"
                            >
                                📋 {project.committee.name}
                            </div>
                        {/if}

                        {#if project.deadline}
                            <div
                                class="flex items-center gap-1 text-[11px] text-muted-foreground"
                            >
                                <Calendar class="size-3" />
                                <span
                                    >{new Date(
                                        project.deadline,
                                    ).toLocaleDateString("it-IT", {
                                        day: "2-digit",
                                        month: "short",
                                        year: "numeric",
                                    })}</span
                                >
                            </div>
                        {/if}
                    </Link>
                {/each}
            </div>
        {:else}
            <div class="rounded-xl border bg-card p-8 text-center">
                <p class="text-sm text-muted-foreground">
                    Nessun progetto assegnato al momento.
                </p>
            </div>
        {/if}
    </div>
</MemberLayout>
