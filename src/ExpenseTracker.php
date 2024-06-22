<?php

namespace App;

use App\Utility\Utility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;


class ExpenseTracker extends Command
{
    const FILE_PATH = 'data.txt';
    const INCOME = 'income';
    const EXPENSE = 'expense';
    private $utility;

    public function __construct(Utility $utility) {
        $this->utility = $utility;
        parent::__construct();
    }

    public static function getDefaultName(): ?string
    {
        return "expense-tracker";
    }

    protected function configure(): void
    {
        $this->setDescription("Personal Income Expense Tracker.");
        $this->setHelp("Run the command: 'expense-tracker' \nParameter/Argument: no input parameter/argument.\nAdd Income: 'category amount' (space separated)\nAdd Expense: 'category amount' (space separated)");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {      
        $helper = $this->getHelper('question'); 
        
        while (true) { 
            // Define the options 
            $question = new ChoiceQuestion( 
                'Please select an option (defaults to "Exit")', 
                ['Add Income', 'Add Expense', 'View Incomes', 'View Expenses', 'View Savings', 'View Categories', 'Exit'], 
                2 
            ); 
            
            $selectedOption = $helper->ask($input, $output, $question);
            
            // Handle the selected option 
            switch ($selectedOption) { 
                case 'Add Income': 
                    add_income:
                    $newIncomeArray=[];
                    $newIncomeUserInput = new Question("  Please insert income information: (e.g. income> salary 20000) \n  income> ");
                    $newIncomeData = $helper->ask($input, $output, $newIncomeUserInput);
                    $newIncome = explode(" ", $newIncomeData);
                    if(count($newIncome)===2){
                        if(is_float((float)$newIncome[1])){
                            if((float)$newIncome[1]>0){
                                $newIncomeArray = [
                                    'status'=> $this::INCOME,
                                    'category'=> $newIncome[0],
                                    'amount' => abs((float)$newIncome[1])
                                ];   
                                $this->utility->writeJsonData($this::FILE_PATH, $newIncomeArray);
                                $output->writeln("\n  <info>Income added.</info>\n");
                            }else{
                                $output->writeln("\n  <error>Please insert positive amount only.</error>\n");
                                goto add_expense;
                            }
                        }else{
                            $output->writeln("\n  <error>Please insert income as prescribed.</error>\n");
                            goto add_income;
                        }
                        
                    }else{
                        $output->writeln("\n  <error>Please insert income as prescribed.</error>\n");
                        goto add_income;
                    }
                    
                    break; 

                case 'Add Expense':
                    add_expense:
                    $newExpenseArray=[];
                    $newExpenseUserInput = new Question("  Please insert expense information: (e.g. expense> rent 20000) \n  expense> ");
                    $newExpenseData = $helper->ask($input, $output, $newExpenseUserInput);
                    $newExpense = explode(" ", $newExpenseData);
                    if(count($newExpense)===2){
                        if(is_float((float)$newExpense[1])){
                            if((float)$newExpense[1] > 0){
                                $newExpenseArray = [
                                    'status'=> $this::EXPENSE,
                                    'category'=> $newExpense[0],
                                    'amount' => abs((float)$newExpense[1])
                                ];
    
                                $this->utility->writeJsonData($this::FILE_PATH, $newExpenseArray);
                                $output->writeln("\n  <info>Expense added.</info>\n");
                            }else{
                                $output->writeln("\n  <error>Please insert positive amount only.</error>\n");
                                goto add_expense;
                            }
                        }else{
                            $output->writeln("\n  <error>Please insert expense as prescribed.</error>\n");
                            goto add_expense;
                        }
                        
                    }else{
                        $output->writeln("\n  <error>Please insert expense as prescribed.</error>\n");
                        goto add_expense;
                    }

                    break; 

                case 'View Incomes':
                    $incomeArray=[];
                    $totalIncome = 0;
                    $getAllData = $this->utility->readJsonData($this::FILE_PATH);
                    $incomeArray = $this->utility->getListByStatus($getAllData,$this::INCOME);
                    if(!$getAllData === false && !$incomeArray===false){
                        
                        $totalIncome = $this->utility->getTotalByStatus($incomeArray, $this::INCOME);

                        $output->writeln("\n**** INCOME LIST ****");
                        $output->writeln("---------------------");
                        foreach($incomeArray as $item){
                            $output->writeln($item['category']." = ".$item['amount']."");
                        }
                        $output->writeln("---------------------");
                        $output->writeln("Total = ".$totalIncome."");
                        $output->writeln("---------------------\n");
                    }else{
                        $output->writeln("\n  <error>There is no Income to show.</error>\n");
                    }

                    break; 

                case 'View Expenses':
                    $expenseArray=[];
                    $totalExpense = 0;
                    $getAllData = $this->utility->readJsonData($this::FILE_PATH);
                    $expenseArray = $this->utility->getListByStatus($getAllData,$this::EXPENSE);
                    if(!$getAllData === false && !$expenseArray===false){

                        $totalExpense = $this->utility->getTotalByStatus($expenseArray, $this::EXPENSE);

                        $output->writeln("\n*** EXPENSE LIST ***");
                        $output->writeln("---------------------");
                        foreach($expenseArray as $item){
                            $output->writeln($item['category']." = ".$item['amount']."");
                        }
                        $output->writeln("---------------------");
                        $output->writeln("Total = ".$totalExpense."");
                        $output->writeln("---------------------\n");
                    }else{
                        $output->writeln("\n  <error>There is no Espense to show.</error>\n");
                    }

                    
                    break; 

                case 'View Savings':
                    $savings = 0;
                    $getAllData = $this->utility->readJsonData($this::FILE_PATH);
                    if(!$getAllData === false){
                        $savings = $this->utility->getSavings($getAllData);

                        $output->writeln($savings>0?"\n  Your Total Saving is: ".$savings."\n":"\n  No Savings.\n");                        

                    }else{
                        $output->writeln( "\n  <error>There is no Saving to show.</error>\n");
                    }
                   

                    break; 

                case 'View Categories':
                    $category = [];
                    $getAllData = $this->utility->readJsonData($this::FILE_PATH);
                    if(!$getAllData === false){
                        $category = $this->utility->getCategory($getAllData);

                        $output->writeln( "\n******** CATEGORY LIST ********");
                        $output->writeln( "--------------------------------");
                        foreach($category as $key => $value){
                            $output->writeln( "Category: ".$key."; Type: ".$value."");
                        }
                        $output->writeln( "--------------------------------\n");

                    }else{
                        $output->writeln("\n  <error>There is no Income/Expense Categories to show.</error>\n");
                    }


                    break; 

                case 'Exit': 
                    $output->writeln("\n Exiting...\n");
                     
                    break 2;

                default: 
                    $output->writeln("\n  <error>Invalid option. Please try again.</error>\n"); 
                    break; 
            }
        }
        return Command::SUCCESS; 
        
    }
}