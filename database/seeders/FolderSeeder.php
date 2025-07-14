<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the SuperAdmin user to create folders
        $superAdmin = User::where('email', 'superadmin@smartprobegroup.com')->first();

        if (!$superAdmin) {
            $this->command->info('SuperAdmin user not found. Please run RolesAndPermissionsSeeder first.');
            return;
        }

        // Create 5 folders for each department
        $departments = [
            'IT' => 'IT Department',
            'Finance' => 'Finance Department',
            'QA' => 'QA Department',
            'HR' => 'HR Department',
            'Purchasing' => 'Purchasing Department',
            'Sales' => 'Sales Department',
            'Operations' => 'Operations Department',
            'General' => 'General/Public'
        ];

        foreach ($departments as $deptKey => $deptName) {
            $folderNames = $this->getDepartmentFolders($deptKey);

            foreach ($folderNames as $folderName => $description) {
                Folder::create([
                    'user_id' => $superAdmin->id,
                    'parent_id' => null, // Root level folders
                    'name' => $folderName,
                    'department' => $deptKey,
                    'description' => $description
                ]);
            }
        }

        $this->command->info('Department folders created successfully - 5 folders per department.');
    }

    /**
     * Get 5 specific folders for each department
     */
    private function getDepartmentFolders(string $department): array
    {
        return match ($department) {
            'IT' => [
                'Network Infrastructure' => 'Network configurations, security protocols, and infrastructure documentation',
                'Software & Licenses' => 'Software documentation, license management, and application guides',
                'Hardware Inventory' => 'Equipment specifications, maintenance records, and hardware documentation',
                'Security Policies' => 'Cybersecurity policies, incident reports, and compliance documentation',
                'System Administration' => 'Server management, backup procedures, and system maintenance guides'
            ],
            'Finance' => [
                'Financial Reports' => 'Monthly, quarterly, and annual financial statements and reports',
                'Budget Planning' => 'Budget proposals, allocations, and financial planning documents',
                'Accounts Payable' => 'Vendor invoices, payment records, and payable documentation',
                'Accounts Receivable' => 'Customer invoices, payment tracking, and receivable records',
                'Tax Documentation' => 'Tax filings, compliance records, and regulatory documentation'
            ],
            'QA' => [
                'Quality Standards' => 'ISO certifications, quality benchmarks, and standard operating procedures',
                'Test Documentation' => 'Test plans, test cases, and quality assurance procedures',
                'Audit Reports' => 'Internal and external audit findings and compliance reports',
                'Process Improvement' => 'Continuous improvement initiatives and process optimization records',
                'Quality Metrics' => 'Performance indicators, quality measurements, and statistical reports'
            ],
            'HR' => [
                'Employee Records' => 'Personnel files, employment contracts, and employee documentation',
                'Recruitment' => 'Job descriptions, candidate profiles, and recruitment processes',
                'Training & Development' => 'Training materials, certification records, and development programs',
                'Benefits Administration' => 'Employee benefits, insurance documentation, and welfare programs',
                'Performance Management' => 'Performance reviews, evaluations, and career development plans'
            ],
            'Purchasing' => [
                'Vendor Management' => 'Supplier contracts, vendor evaluations, and partnership agreements',
                'Purchase Orders' => 'PO documentation, order tracking, and procurement records',
                'Procurement Policies' => 'Purchasing guidelines, approval workflows, and procurement procedures',
                'Cost Analysis' => 'Price comparisons, cost-benefit analyses, and procurement analytics',
                'Contract Management' => 'Service agreements, contract renewals, and legal documentation'
            ],
            'Sales' => [
                'Customer Contracts' => 'Sales agreements, customer contracts, and service level agreements',
                'Sales Proposals' => 'Client proposals, quotations, and sales presentations',
                'Marketing Materials' => 'Brochures, promotional content, and marketing campaigns',
                'Sales Reports' => 'Performance metrics, sales analytics, and revenue tracking',
                'Customer Relations' => 'Client communications, feedback records, and relationship management'
            ],
            'Operations' => [
                'Standard Procedures' => 'Operating procedures, workflow documentation, and process guides',
                'Equipment Management' => 'Equipment manuals, maintenance schedules, and operational guides',
                'Safety Protocols' => 'Workplace safety procedures, incident reports, and compliance documentation',
                'Production Records' => 'Output tracking, quality control records, and production metrics',
                'Supply Chain' => 'Logistics documentation, inventory management, and supplier coordination'
            ],
            'General' => [
                'Company Policies' => 'General company-wide policies, procedures, and governance documents',
                'Board Documents' => 'Board resolutions, meeting minutes, and corporate governance materials',
                'Legal & Compliance' => 'Legal contracts, regulatory compliance, and corporate legal documentation',
                'Communications' => 'Company announcements, newsletters, and internal communications',
                'Strategic Planning' => 'Business plans, strategic initiatives, and organizational development'
            ],
            default => []
        };
    }
}
