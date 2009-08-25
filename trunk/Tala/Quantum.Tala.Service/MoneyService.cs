using System;
using GURUCORE.Framework.Business;
using GURUCORE.Framework.DataAccess;
using GURUCORE.Framework.DataAccess.ORMapping;
using Quantum.Tala.Lib;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Lib.Core.Security.Cryptography;
using System.Data.Common;
using GURUCORE.Framework.Core;


namespace Quantum.Tala.Service
{
    public class MoneyService : BusinessService, Quantum.Tala.Service.IMoneyService
    {
        [TransactionBound]
        public virtual transactionDTO CreateTransation(transactionDTO p_dto)
        {
            return DAU.AddObject<transactionDTO>(p_dto);
        }

        [TransactionBound]
        public virtual int SaveTransation(transactionDTO p_dto)
        {
            return DAU.SaveSingleObject<transactionDTO>(p_dto);
        }

    }
}
