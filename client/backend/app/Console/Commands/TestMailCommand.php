<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email=test@example.com}';
    protected $description = 'Test mail configuration by sending a test email';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧪 Testing mail configuration...");
        $this->info("📧 Sending test email to: {$email}");
        $this->newLine();
        
        try {
            Mail::raw('This is a test email from TravelVN. If you received this, mail configuration is working! 🎉', function(Message $m) use ($email) {
                $m->to($email)
                  ->subject('Test Email - TravelVN Configuration Check');
            });
            
            $this->info('✅ Email sent successfully!');
            $this->newLine();
            
            $mailer = config('mail.mailer');
            
            if ($mailer === 'log') {
                $this->warn('⚠️  WARNING: Mail driver is set to "log"');
                $this->info('📝 Check email logs in: storage/logs/laravel.log');
                $this->info('💡 To send real emails, update MAIL_MAILER in .env to "smtp"');
            } else {
                $this->info("✅ Mail driver configured as: {$mailer}");
                $this->info("📬 Check your email at: {$email}");
                $this->info('⏳ Email should arrive within 5 minutes');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error sending email:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->info('🔧 Troubleshooting:');
            $this->info('1. Check MAIL_MAILER is set correctly in .env');
            $this->info('2. Check MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD');
            $this->info('3. Check internet connection');
            $this->info('4. Run: php artisan config:cache');
            
            return 1;
        }
        
        return 0;
    }
}
