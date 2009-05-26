using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using TalaAPI.Business;

namespace TestBusiness
{
    class Program
    {
        
        static void Main(string[] args)
        {
            Card c1 = new Card("01","r");
            Card c2 = new Card("01", "t");
            Card c3 = new Card("02", "r");

            Card[] arrCard = { c1, c2 , c3};
            
            Console.WriteLine(c1 == c2);
            Console.WriteLine(c1 > c2);
            Console.WriteLine(c1 < c2);

            arrCard.IsValidPhom();            
            Console.WriteLine(arrCard.ToTalaString());

            Console.ReadLine();
        }
    }
}
