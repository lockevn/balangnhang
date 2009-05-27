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
            Card c3 = new Card("01", "r");

            c2.Pos = 2;
            c3.Pos = 3;
            Card c4 = null;


            List<Card> baitrentay = new List<Card>();

            baitrentay.Add(c1);
            baitrentay.Add(c2);

            bool b = baitrentay.Contains(c3);

            if (c4 == null)
            {
                Console.WriteLine("C1 is null");
            }

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
