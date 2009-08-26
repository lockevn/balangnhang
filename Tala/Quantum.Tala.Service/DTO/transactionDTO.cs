using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Quantum.Tala.Service.Business;

namespace Quantum.Tala.Service.DTO
{
    public sealed partial class transactionDTO
    {
        public const string STATUS_PROCESSING = "processing";
        public const string STATUS_OK = "ok";
        public const string STATUS_FAILED = "failed";

        public new string ToString()
        {

            return string.Format("{0},{1},{2},{3},{4},{5},{6},{7}",
                this.amount,
                    this.desc,
                    this.meta,
                    this.meta1,
                    this.meta2,
                    this.meta3,
                    this.type.ToString(),
                    this.status
                    );
        }
    }
}
