<div>
    <x-drawer
        id="calendar-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando calendario"
        separator
    >
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                @click="$wire.open = false"
            />
            @if($action === 'edit')
                <x-button
                    label="Eliminar"
                    icon="o-trash"
                    class="btn-accent btn-outline rounded-3xl !uppercase"
                    wire:click="delete"
                    spinner="delete"
                    wire:target="cover,delete,submit"
                    wire:loading.attr="disabled"
                />
            @endif
            <x-button
                label="Guardar"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                wire:click="submit"
                spinner="submit"
                wire:target="cover,delete,submit"
                wire:loading.attr="disabled"
            />
        </x-slot:actions>

        <div>
            <x-tabs
                wire:model="tab_selected"
                active-class="btn !btn-accent !text-white rounded-3xl space-x-5"
                label-class="btn btn-secondary rounded-3xl text-white text-md font-bold"
                label-div-class="bg-white space-x-1.5"
            >
                <x-tab name="general" label="General" icon="s-home">
                    <x-form wire:submit.prevent="submit" class="gap-5">
                        <div wire:key="{{ $this->cover_key }}">
                            <x-file
                                label="Portada"
                                wire:model="cover"
                                change-text="Seleccionar imagen"
                                accept="image/png, image/jpeg"
                                class="w-2/3 md:w-full max-w-full"
                            >
                                <img src="{{ $this->coverThumbnail }}" alt="Portada" class="w-60 rounded-3xl">
                            </x-file>
                        </div>

                        <x-input
                            label="Titulo"
                            class="rounded-3xl max-w-full"
                            wire:model="form.name"
                            required
                        />
                
                        <x-toggle
                            label="Activo"
                            class="rounded-3xl"
                            wire:model="form.is_active"
                        />

                        <x-editor
                            label="Descripción"
                            class="rounded-3xl w-2/3 md:w-full"
                            wire:model="form.description"
                            :config="$tinyMCE_settings"
                        />
                    </x-form>
                </x-tab>

                @if($action === 'edit')
                    <x-tab name="services" label="Servicios" icon="s-building-storefront" class="space-y-5">
                        <x-choices
                            label="Agregar servicios"
                            class="rounded-3xl"
                            wire:model="form.services"
                            :options="$services"
                        />

                        <x-alert title="En caso de que elimines un servicio que ya cuente con reservas o citas, estas serán eliminadas" icon="s-exclamation-triangle" class="bg-accent text-white !rounded-3xl" />
                    </x-tab>

                    <x-tab name="schedule" label="Citas" icon="s-calendar" class="space-y-5">
                        <div id="calendar-availability-single">
                            <h4 class="text-lg font-medium">Añadir cita individual</h4>
                            <x-form wire:submit.prevent="saveAvailability" class="gap-5 mt-5">
                                <x-datetime
                                    label="Fecha"
                                    wire:model="single_date"
                                    icon="o-calendar"
                                    class="rounded-3xl"
                                />
                                <x-datetime
                                    label="Hora"
                                    wire:model="single_time"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                    type="time"
                                />
                                <x-input
                                    label="Duración en minutos"
                                    wire:model="duration"
                                    type="number" step="1" min="5"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                />
                                <x-input
                                    label="¿Cuántos clientes pueden reservar esta misma cita?"
                                    type="number"
                                    min="1"
                                    icon="s-users"
                                    wire:model="single_capacity"
                                    class="rounded-3xl"
                                />
                                <x-button
                                    label="Crear cita"
                                    class="btn-primary rounded-3xl"
                                    wire:click="saveAvailability"
                                    spinner="saveAvailability"
                                />
                            </x-form>       
                        </div>
                        <div div="calendar-availability-multiple">
                            <h4 class="text-lg font-medium">Añadir citas múltiples</h4>
                            <x-form wire:submit.prevent="saveAvailabilities" class="gap-5 mt-5">
                                <x-datetime
                                    label="Desde"
                                    wire:model="start_date"
                                    icon="o-calendar"
                                    class="rounded-3xl"
                                />
                                <x-datetime
                                    label="Hasta"
                                    wire:model="end_date"
                                    icon="o-calendar"
                                    class="rounded-3xl"
                                />
                                <x-datetime
                                    label="Desde"
                                    wire:model="start_time"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                    type="time"
                                />
                                <x-datetime
                                    label="Hasta"
                                    wire:model="end_time"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                    type="time"
                                />
                                <hr>
                                <x-datetime
                                    label="Desde"
                                    wire:model="second_start_time"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                    type="time"
                                />
                                <x-datetime
                                    label="Hasta"
                                    wire:model="second_end_time"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                    type="time"
                                />

                                <x-input
                                    label="¿Cada cuántos minutos quieres crear una cita?"
                                    wire:model="duration"
                                    icon="o-clock"
                                    class="rounded-3xl"
                                />

                                <x-input
                                    label="¿Cuántos clientes pueden reservar esta misma cita?"
                                    icon="s-users"
                                    wire:model="capacity"
                                    class="rounded-3xl"
                                />
                                <x-choices
                                    label="¿Qué dias de la semana se crearán las citas?"
                                    :options="$select_days"
                                    wire:model="days"
                                    class="rounded-3xl"
                                    multiple
                                />

                                @if(!empty($message))
                                    <x-alert :title="$message" icon="s-exclamation-triangle" class="bg-accent text-white !rounded-3xl" />
                                @endif

                                <x-button
                                    label="{{ empty($message) ? 'Calcular citas' : 'Confirmar citas' }}"
                                    class="btn-primary rounded-3xl"
                                    wire:click="saveAvailabilities({{ empty($message) ? 0 : 1 }})"
                                    spinner="saveAvailabilities"
                                />
                            </x-form>
                        </div>
                    </x-tab>

                    <x-tab name="options" label="Opciones" icon="s-cog-6-tooth">
                        <div class="space-y-7">
                            <div class="space-y-3">
                                <h4 class="text-lg font-medium">General</h4>

                                <x-toggle
                                    label="Mostrar solo citas libres en la página de reservas"
                                    class="rounded-3xl"
                                    wire:model="form.show_busy"
                                />
                            </div>

                            <div class="space-y-3">
                                <h4 class="text-lg font-medium">Datos de contacto y facturación</h4>

                                <x-toggle
                                    label="Dirección"
                                    class="rounded-3xl"
                                    wire:model="form.require_address"
                                />

                                <x-toggle
                                    label="NIF / DNI"
                                    class="rounded-3xl"
                                    wire:model="form.require_nif_document"
                                />

                                <x-toggle
                                    label="Datos de otros asistentes"
                                    class="rounded-3xl"
                                    wire:model="form.require_assistants"
                                />
                            </div>

                            <div>
                                <h4 class="text-lg font-medium">Formulario de contacto</h4>

                                <x-button
                                    label="Añadir pregunta"
                                    icon="s-plus"
                                    class="btn-primary btn-sm btn-outline rounded-3xl !uppercase mt-3"
                                    wire:click="addQuestion"
                                />

                                <div class="mt-3 space-y-5" wire:sortable="updateQuestionPosition" wire:key="{{ $questions_key }}">
                                    @foreach($questions as $question)
                                        <div
                                            wire:sortable.item="{{ $question['calendar_question_id'] }}"
                                            wire:key="calendar-question-{{ $question['calendar_question_id'] }}"
                                            class="p-5 shadow-lg rounded-3xl space-y-5"
                                        >
                                            <x-toggle
                                                label="Activo"
                                                class="rounded-3xl"
                                                wire:model="questions.{{ $loop->index }}.is_active"
                                            />

                                            <x-input
                                                label="Consulta"
                                                class="rounded-3xl max-w-full"
                                                wire:model="questions.{{ $loop->index }}.question"
                                            />

                                            <x-choices
                                                label="¿A qué servicios quieres aplicar esta pregunta?"
                                                class="rounded-3xl"
                                                wire:model="questions.{{ $loop->index }}.services"
                                                :options="$this->calendarServicesOptions"
                                            />

                                            <x-toggle
                                                label="Requerido"
                                                class="rounded-3xl"
                                                wire:model="questions.{{ $loop->index }}.is_required"
                                            />

                                            <div class="flex gap-1">
                                                <x-button
                                                    icon="s-trash"
                                                    class="btn-accent btn-outline btn-circle btn-sm"
                                                    wire:click="removeQuestion({{ $loop->index }})"
                                                />
        
                                                <x-button
                                                    icon="s-arrows-pointing-out"
                                                    class="btn-circle btn-ghost text-primary rounded-3xl btn-sm"
                                                    wire:sortable.handle
                                                />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </x-tab>
                @endif
            </x-tabs>
        </div>
    </x-drawer>
</div>
