<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('articles')->insert([
            [
                'id' => Str::uuid()->toString(),
                'title' => 'About WrapCrop: QR-per-Tree Farm Tracking System',
                'slug' => 'about-wrapcrop-qr-per-tree-farm-tracking-system',
                'content' => '<h2>Smart Farming for Filipino Jackfruit Farmers</h2>
                <p><strong>WrapCrop</strong> is a digital farming platform that helps Filipino jackfruit farmers like <strong>IMYV Jackfruit Farm</strong> track trees, monitor yields, and grow their harvest. Built with real farmers in mind, it combines <strong>QR code tracking</strong>, <strong>GPS mapping</strong>, and <strong>weight-based reporting</strong> into one simple system.</p>
                
                <h3>How It Works</h3>
                <p>The system follows three simple steps to smarter farming:</p>
                <ol>
                    <li><strong>Register Trees</strong> — Assign unique QR codes to each jackfruit tree and log their location using GPS (takes only 1-2 minutes per tree).</li>
                    <li><strong>Track Harvest</strong> — Scan QR codes during harvest and log the weight per tree (less than 1 minute per scan).</li>
                    <li><strong>Monitor & Improve</strong> — View analytics to identify your best-performing trees and reduce losses with real-time insights.</li>
                </ol>
                
                <h3>Key Features</h3>
                <ul>
                    <li><strong>QR Code Tracking:</strong> Every tree gets its own unique QR code — like an ID card for easy monitoring. Simply scan with your smartphone for instant tree history.</li>
                    <li><strong>GPS Mapping:</strong> Know exactly where each tree is located on your farm with tree location tracking, farm layout view, and easy navigation.</li>
                    <li><strong>Yield Monitoring:</strong> Log harvested weight per tree to track productivity with per-tree tracking, season comparison, and yield forecasting.</li>
                    <li><strong>Loss Reporting:</strong> Report losses with specific reasons to identify problems early with disease tracking and loss analytics.</li>
                    <li><strong>Offline First:</strong> Works without internet — automatically syncs when you\'re back online. No signal needed, auto-sync, and remote-ready.</li>
                    <li><strong>Analytics Dashboard:</strong> View reports and insights to improve your harvest with live dashboards, export reports, and trend analysis.</li>
                </ul>
                
                <h3>Why WrapCrop Matters</h3>
                <p>For small to medium jackfruit farmers in the Philippines, tracking hundreds of trees manually is nearly impossible. WrapCrop solves this by digitizing every tree, making it easy to:</p>
                <ul>
                    <li>Know exactly which trees produce the most fruit</li>
                    <li>Detect disease or underperforming trees early</li>
                    <li>Reduce post-harvest losses through better monitoring</li>
                    <li>Make data-driven decisions for farm improvement</li>
                </ul>
                
                <blockquote>"WrapCrop isn\'t just a tool — it\'s our commitment to helping Filipino farmers grow smarter and harvest better." — WrapCrop Team</blockquote>
                
                <h3>Our Mission & Vision</h3>
                <p><strong>Mission:</strong> Empower Filipino jackfruit farmers with simple, accessible technology that increases yields and reduces losses.</p>
                <p><strong>Vision:</strong> Become the trusted farm management platform for tropical fruit farmers across the Philippines.</p>
                
                <h3>Partner Farm</h3>
                <p><strong>IMYV Jackfruit Farm</strong> is among the first farms to adopt WrapCrop — helping us build better tools for real farmers. Their feedback and real-world use shape every feature we develop.</p>
                
                <h3>System Goals by 2027</h3>
                <ul>
                    <li>1,000+ trees tracked across partner farms</li>
                    <li>2+ partner farms onboarded</li>
                    <li>100% farm digitization for all partner farms</li>
                    <li>Reduce crop waste by 75%</li>
                    <li>Maintain 98% data accuracy</li>
                </ul>
                
                <p><strong>Ready to digitalize your farm? Join WrapCrop today.</strong></p>',
                'topic' => 'About System',
                'featured_image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&q=80',
                'published_at' => Carbon::now()->subDays(3),
                'is_published' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}