<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\FinancialTransaction;
use App\Models\BlockedTime;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpar os dados existentes para evitar duplicação em testes (opcional, mas recomendado)
        // Desativar restrições de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('users')->where('email', '!=', 'admin@barberflow.com')->delete(); // Preserva o admin default se houver
        BlockedTime::truncate();
        Appointment::truncate();
        FinancialTransaction::truncate();
        Service::truncate();
        Barber::truncate();

        // Reativar restrições
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Criar Barbeiros
        $barber1 = Barber::create([
            'nome' => 'João Silva (Master)',
            'email' => 'joao@barbearia.com',
            'telefone' => '11999999991',
            'ativo' => true,
            'status' => 'livre'
        ]);

        $barber2 = Barber::create([
            'nome' => 'Pedro Santos (Barbeiro)',
            'email' => 'pedro@barbearia.com',
            'telefone' => '11999999992',
            'ativo' => true,
            'status' => 'livre'
        ]);

        $barber3 = Barber::create([
            'nome' => 'Marcos Paulo (Barbeiro)',
            'email' => 'marcos@barbearia.com',
            'telefone' => '11999999993',
            'ativo' => true,
            'status' => 'ausente'
        ]);

        // 3. Criar Serviços
        $cabelo = Service::create([
            'nome' => 'Corte de Cabelo (Degradê)',
            'descricao' => 'Corte masculino na tesoura ou máquina com degradê perfeito.',
            'valor' => 35.00,
            'duracao' => 30,
            'ativo' => true
        ]);

        $barba = Service::create([
            'nome' => 'Barba Terapia',
            'descricao' => 'Barba com toalha quente e massagem facial.',
            'valor' => 25.00,
            'duracao' => 30,
            'ativo' => true
        ]);

        $combo = Service::create([
            'nome' => 'Combo: Cabelo + Barba',
            'descricao' => 'Serviço completo de cabelo e barba.',
            'valor' => 55.00,
            'duracao' => 60,
            'ativo' => true
        ]);

        $sobrancelha = Service::create([
            'nome' => 'Sobrancelha (Navalha)',
            'descricao' => 'Alinhamento de sobrancelha na navalha.',
            'valor' => 15.00,
            'duracao' => 15,
            'ativo' => true
        ]);

        // 4. Criar Usuários (Admin e Funcionários)
        
        // Garante que o Admin existe
        User::updateOrCreate(
            ['email' => 'admin@barberflow.com'],
            [
                'name' => 'Administrador Supremo',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'barber_id' => null
            ]
        );

        // Usuário Funcionário 1 (João)
        User::updateOrCreate(
            ['email' => 'joao@barberflow.com'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('12345678'),
                'role' => 'employee',
                'barber_id' => $barber1->id
            ]
        );

        // Usuário Funcionário 2 (Pedro)
        User::updateOrCreate(
            ['email' => 'pedro@barberflow.com'],
            [
                'name' => 'Pedro Santos',
                'password' => Hash::make('12345678'),
                'role' => 'employee',
                'barber_id' => $barber2->id
            ]
        );

        // 5. Criar Agendamentos (Para o dia atual e o dia seguinte)
        $hoje = Carbon::today()->format('Y-m-d');
        $amanha = Carbon::tomorrow()->format('Y-m-d');
        $ontem = Carbon::yesterday()->format('Y-m-d');

        // Ontem (Concluídos)
        Appointment::create([
            'cliente_nome' => 'Lucas Moura',
            'cliente_whatsapp' => '11988887777',
            'barber_id' => $barber1->id,
            'service_id' => $combo->id,
            'data' => $ontem,
            'hora' => '10:00',
            'status' => 'concluido'
        ]);

        Appointment::create([
            'cliente_nome' => 'Roberto Carlos',
            'cliente_whatsapp' => '11977776666',
            'barber_id' => $barber2->id,
            'service_id' => $cabelo->id,
            'data' => $ontem,
            'hora' => '14:30',
            'status' => 'concluido'
        ]);

        // Hoje (Agendados e Cancelados)
        Appointment::create([
            'cliente_nome' => 'Carlos Eduardo',
            'cliente_whatsapp' => '11911112222',
            'barber_id' => $barber1->id,
            'service_id' => $barba->id,
            'data' => $hoje,
            'hora' => '09:00',
            'status' => 'concluido' // Já foi feito cedo
        ]);

        Appointment::create([
            'cliente_nome' => 'Thiago Silva',
            'cliente_whatsapp' => '11922223333',
            'barber_id' => $barber2->id,
            'service_id' => $combo->id,
            'data' => $hoje,
            'hora' => '11:00',
            'status' => 'cancelado'
        ]);

        Appointment::create([
            'cliente_nome' => 'Fernando Alisson',
            'cliente_whatsapp' => '11933334444',
            'barber_id' => $barber1->id,
            'service_id' => $cabelo->id,
            'data' => $hoje,
            'hora' => '15:00',
            'status' => 'agendado'
        ]);

        Appointment::create([
            'cliente_nome' => 'Gabriel Jesus',
            'cliente_whatsapp' => '11955556666',
            'barber_id' => $barber2->id,
            'service_id' => $sobrancelha->id,
            'data' => $hoje,
            'hora' => '16:30',
            'status' => 'agendado'
        ]);

        // Amanhã (Agendados)
        Appointment::create([
            'cliente_nome' => 'Marcelo Vieira',
            'cliente_whatsapp' => '11944445555',
            'barber_id' => $barber1->id,
            'service_id' => $combo->id,
            'data' => $amanha,
            'hora' => '10:00',
            'status' => 'agendado'
        ]);

        // 6. Criar Bloqueios de Agenda (Fila/Horário de Almoço)
        BlockedTime::create([
            'barber_id' => $barber1->id,
            'date' => $hoje,
            'start_time' => '12:00',
            'end_time' => '13:00',
            'reason' => 'Horário de Almoço'
        ]);

        BlockedTime::create([
            'barber_id' => $barber2->id,
            'date' => $hoje,
            'start_time' => '13:00',
            'end_time' => '14:00',
            'reason' => 'Horário de Almoço'
        ]);
        
        BlockedTime::create([
            'barber_id' => $barber3->id,
            'date' => $hoje,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'reason' => 'Folga / Atestado'
        ]);

        // 7. Criar Transações Financeiras (Receitas e Despesas)
        
        // Entradas (dos serviços de ontem)
        FinancialTransaction::create([
            'tipo' => 'entrada',
            'descricao' => 'Serviço: Combo: Cabelo + Barba (Cliente: Lucas Moura) - João',
            'valor' => 55.00,
            'data' => $ontem
        ]);

        FinancialTransaction::create([
            'tipo' => 'entrada',
            'descricao' => 'Serviço: Corte de Cabelo (Degradê) (Cliente: Roberto Carlos) - Pedro',
            'valor' => 35.00,
            'data' => $ontem
        ]);
        
        // Entradas (dos serviços de hoje cedo)
        FinancialTransaction::create([
            'tipo' => 'entrada',
            'descricao' => 'Serviço: Barba Terapia (Cliente: Carlos Eduardo) - João',
            'valor' => 25.00,
            'data' => $hoje
        ]);
        
        // Despesas (Contas do dia a dia)
        FinancialTransaction::create([
            'tipo' => 'saida',
            'descricao' => 'Pagamento de Conta de Luz (Enel)',
            'valor' => 150.00,
            'data' => $hoje
        ]);
        
        FinancialTransaction::create([
            'tipo' => 'saida',
            'descricao' => 'Compra de Produtos (Giletes, Shaving, Toalhas)',
            'valor' => 230.50,
            'data' => $ontem
        ]);
        
        FinancialTransaction::create([
            'tipo' => 'saida',
            'descricao' => 'Manutenção da Máquina de Cortar Cabelo',
            'valor' => 85.00,
            'data' => $hoje
        ]);

        $this->command->info('✅ Dados de teste gerados com sucesso!');
        $this->command->info('Você já pode testar o sistema completo. Senha para todos os usuários é: 12345678');
    }
}
