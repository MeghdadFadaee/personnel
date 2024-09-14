<?php

namespace Illuminate\Database\Query {
    /**
     * @see \App\Providers\AppServiceProvider::boot()
     * @method static \Illuminate\Database\Query\Builder mine(string $column = 'user_id')
     */
    class Builder {}
}

namespace Filament\Tables {
    /**
     * @see \App\Providers\AppServiceProvider::boot()
     * @method static \Filament\Tables\Table toggleableAll()
     */
    class Table {}
}

namespace Filament\Forms\Components {
    /**
     * @see \App\Providers\AppServiceProvider::boot()
     * @method static \Filament\Forms\Components\Select setTitle(string $attribute)
     */
    class Select {}

    /**
     * @see \App\Providers\AppServiceProvider::boot()
     * @method static \Filament\Forms\Components\TextInput time()
     */
    class TextInput {}
}

namespace Filament\Tables\Columns {
    /**
     * @see \App\Providers\AppServiceProvider::boot()
     * @method static \Filament\Tables\Columns\TextInputColumn time()
     */
    class TextInputColumn {}
}
